pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.classificationStoreByNames");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.classificationStoreByNames = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'classificationStoreByNames',
    dataApplied: false,
    dataObjectClassId: null,
    transformationResultType: null,

    buildSettingsForm: function () {
        if (!this.form) {
            this.dataObjectClassId = this.configItemRootContainer.currentDataValues.dataObjectClassId;
            this.transformationResultType = this.initContext.mappingConfigItemContainer.currentDataValues.transformationResultType;

            let languages = [''];
            languages = languages.concat(pimcore.settings.websiteLanguages);

            const languageSelection = Ext.create('Ext.form.ComboBox', {
                store: languages,
                forceSelection: true,
                fieldLabel: t('language'),
                name: this.dataNamePrefix + 'language',
                value: this.data.language,
                allowBlank: true,
                hidden: true
            });

            const clsGroupName = Ext.create('Ext.form.TextField', {
                fieldLabel: 'Group Name',
                name: this.dataNamePrefix + 'groupName',
                value: this.data.groupName,
                editable: false,
                disabled: true
            });

            const clsKeyName = Ext.create('Ext.form.TextField', {
                fieldLabel: 'Key Name',
                name: this.dataNamePrefix + 'keyName',
                value: this.data.keyName,
                editable: false,
                disabled: true
            });

            const clsKeySelection = Ext.create('Ext.form.FieldContainer', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_classification_store_key'),
                layout: 'hbox',
                items: [
                    this.clsKeySelectionLabel,
                    {
                        xtype: "button",
                        iconCls: "pimcore_icon_search",
                        style: "margin-left: 5px",
                        handler: function () {

                            let searchWindow = new pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.tools.classificationStoreKeySearchWindow(
                                this.dataObjectClassId,
                                attributeSelection.getValue(),
                                this.transformationResultType,
                                function (id, groupName, keyName) {
                                    clsGroupName.setValue(groupName);
                                    clsKeyName.setValue(keyName);
                                }.bind(this)
                            );
                            searchWindow.show();
                        }.bind(this)
                    }
                ],
                width: 600,
                border: false,
                hidden: true,
                style: {
                    padding: 0
                },
            });


            const attributeStore = Ext.create('Ext.data.JsonStore', {
                fields: ['key', 'name', 'localized'],
                listeners: {
                    dataChanged: function (store) {
                        if (!this.dataApplied) {
                            attributeSelection.setValue(this.data.fieldName);
                            if (this.form) {
                                this.form.isValid();
                            }
                            this.dataApplied = true;
                            this.setLanguageVisibility(attributeStore, attributeSelection, languageSelection, clsKeySelection, clsGroupName, clsKeyName);
                        }

                        if (!store.findRecord('key', attributeSelection.getValue())) {
                            attributeSelection.setValue(null);
                            this.form.isValid();
                        }
                    }.bind(this)
                }
            });

            const attributeSelection = Ext.create('Ext.form.ComboBox', {
                displayField: 'title',
                valueField: 'key',
                queryMode: 'local',
                forceSelection: true,
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_fieldName'),
                name: this.dataNamePrefix + 'fieldName',
                value: this.data.fieldName,
                allowBlank: false,
                msgTarget: 'under'
            });

            attributeSelection.setStore(attributeStore);
            attributeSelection.on('change', this.setLanguageVisibility.bind(this, attributeStore, attributeSelection, languageSelection, clsKeySelection, clsGroupName, clsKeyName));

            //register listeners for class and type changes
            this.initContext.mappingConfigItemContainer.on(pimcore.plugin.pimcoreDataImporterBundle.configuration.events.transformationResultTypeChanged, function (newType) {
                this.transformationResultType = newType;
                this.clsKeySelectionLabel.setValue('');
                clsKeySelectionValue.setValue('');
            }.bind(this));
            this.configItemRootContainer.on(pimcore.plugin.pimcoreDataImporterBundle.configuration.events.classChanged,
                function (combo, newValue, oldValue) {
                    this.dataObjectClassId = newValue;
                    this.initAttributeStore(attributeStore);
                }.bind(this)
            );

            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 120,
                    width: 500,
                    listeners: {
                        errorchange: this.initContext.updateValidationStateCallback
                    }
                },
                border: false,
                items: [
                    attributeSelection,
                    clsKeySelection,
                    clsKeyName,
                    clsGroupName,
                    languageSelection
                ]
            });

            //special loading strategy to prevent hundreds of requests when loading configurations
            this.initAttributeStore(attributeStore);
        }

        return this.form;
    },

    initAttributeStore: function (attributeStore) {

        const classId = this.dataObjectClassId;
        // const transformationResultType = this.transformationResultType;

        let classificationStoreFieldCache = this.configItemRootContainer.classificationStoreFieldCache || {};

        if (classificationStoreFieldCache[classId]) {

            if (classificationStoreFieldCache[classId].loading) {
                setTimeout(this.initAttributeStore.bind(this, attributeStore), 400);
            } else {
                attributeStore.loadData(classificationStoreFieldCache[classId].data);
            }


        } else {
            classificationStoreFieldCache = classificationStoreFieldCache || {};
            classificationStoreFieldCache[classId] = {
                loading: true,
                data: null
            };
            this.configItemRootContainer.classificationStoreFieldCache = classificationStoreFieldCache;

            Ext.Ajax.request({
                url: Routing.generate('pimcore_dataimporter_configdataobject_loaddataobjectclassificationstoreattributes'),
                method: 'GET',
                params: {
                    'class_id': classId
                },
                success: function (response) {
                    let data = Ext.decode(response.responseText);

                    classificationStoreFieldCache[classId].loading = false;
                    classificationStoreFieldCache[classId].data = data.attributes;

                    attributeStore.loadData(classificationStoreFieldCache[classId].data);

                }.bind(this)
            });
        }
    },

    setLanguageVisibility: function (attributeStore, attributeSelection, languageSelection, clsKeySelection, clsGroupName, clsKeyName) {
        const record = attributeStore.findRecord('key', attributeSelection.getValue());
        if (record) {
            languageSelection.setHidden(!record.data.localized);
            clsKeySelection.show();
            clsGroupName.show();
            clsKeyName.show();

        } else {
            clsKeySelection.hide();
            clsGroupName.hide();
            clsKeyName.hide();
        }
    },
});
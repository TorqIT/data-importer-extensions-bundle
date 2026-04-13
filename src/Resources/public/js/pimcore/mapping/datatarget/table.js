pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.table");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.table = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'table',
    dataApplied: false,
    dataObjectClassId: null,
    transformationResultType: null,

    buildSettingsForm: function () {

        if (!this.form) {
            this.dataObjectClassId = this.configItemRootContainer.currentDataValues.dataObjectClassId;
            this.transformationResultType = this.initContext.mappingConfigItemContainer.currentDataValues.transformationResultType;

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
                        }
                        if (!store.findRecord('key', attributeSelection.getValue())) {
                            attributeSelection.setValue(null);
                            this.form.isValid();
                        }
                    }.bind(this)
                }
            });

            attributeSelection.setStore(attributeStore);

            this.initContext.mappingConfigItemContainer.on(pimcore.plugin.pimcoreDataImporterBundle.configuration.events.transformationResultTypeChanged, function (newType) {
                this.transformationResultType = newType;
                this.initAttributeStore(attributeStore);
            }.bind(this));
            this.configItemRootContainer.on(pimcore.plugin.pimcoreDataImporterBundle.configuration.events.classChanged,
                function (combo, newValue, oldValue) {
                    this.dataObjectClassId = newValue;
                    this.initAttributeStore(attributeStore);
                }.bind(this)
            );

            const writeIfTargetIsNotEmpty = Ext.create('Ext.form.Checkbox', {
                boxLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dataTarget.type_direct_write_settings_ifTargetIsNotEmpty'),
                name: this.dataNamePrefix + 'writeIfTargetIsNotEmpty',
                value: this.data.hasOwnProperty('writeIfTargetIsNotEmpty') ? this.data.writeIfTargetIsNotEmpty : true,
                inputValue: true,
                uncheckedValue: false,
                listeners: {
                    change: function (checkbox, value) {
                        if (value) {
                            writeIfSourceIsEmpty.setReadOnly(false);
                            writeIfSourceIsEmpty.setValue(true);
                        } else {
                            writeIfSourceIsEmpty.setValue(false);
                            writeIfSourceIsEmpty.setReadOnly(true);
                        }
                    }
                }
            });

            const writeIfSourceIsEmpty = Ext.create('Ext.form.Checkbox', {
                boxLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dataTarget.type_direct_write_settings_ifSourceIsEmpty'),
                name: this.dataNamePrefix + 'writeIfSourceIsEmpty',
                value: this.data.hasOwnProperty('writeIfSourceIsEmpty') ? this.data.writeIfSourceIsEmpty : true,
                uncheckedValue: false,
                readOnly: this.data.hasOwnProperty('writeIfTargetIsNotEmpty') ? !this.data.writeIfTargetIsNotEmpty : false,
                inputValue: true
            });

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
                    {
                        xtype: 'fieldcontainer',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dataTarget.type_direct_write_settings_label'),
                        defaultType: 'checkboxfield',
                        items: [writeIfTargetIsNotEmpty, writeIfSourceIsEmpty]
                    }
                ]
            });

            this.initAttributeStore(attributeStore);
        }

        return this.form;
    },

    initAttributeStore: function (attributeStore) {
        var classId = this.dataObjectClassId;

        Ext.Ajax.request({
            url: Routing.generate('pimcore_dataimporter_configdataobject_loaddataobjectattributes'),
            method: 'GET',
            params: {
                'class_id': classId,
                'transformation_result_type': 'table',
                'system_write': 1
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                attributeStore.loadData(data.attributes || []);
            }.bind(this)
        });
    }
});

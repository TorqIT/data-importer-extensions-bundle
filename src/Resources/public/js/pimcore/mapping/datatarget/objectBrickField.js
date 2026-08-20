pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.objectBrickField");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.objectBrickField = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'objectBrickField',
    brickFieldDataApplied: false,
    attributeDataApplied: false,
    dataObjectClassId: null,

    buildSettingsForm: function () {

        if (!this.form) {
            this.dataObjectClassId = this.configItemRootContainer.currentDataValues.dataObjectClassId;

            const attributeSelection = Ext.create('Ext.form.ComboBox', {
                displayField: 'name',
                valueField: 'key',
                queryMode: 'local',
                forceSelection: true,
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_objectBrick_attribute'),
                name: this.dataNamePrefix + 'fieldName',
                value: this.data.fieldName,
                allowBlank: false,
                msgTarget: 'under'
            });

            const attributeStore = Ext.create('Ext.data.JsonStore', {
                fields: ['key', 'name'],
                listeners: {
                    dataChanged: function (store) {
                        if (!this.attributeDataApplied) {
                            attributeSelection.setValue(this.data.fieldName);
                            if (this.form) this.form.isValid();
                            this.attributeDataApplied = true;
                        }

                        if (!store.findRecord('key', attributeSelection.getValue())) {
                            attributeSelection.setValue(null);
                            this.form.isValid();
                        }
                    }.bind(this)
                }
            });

            attributeSelection.setStore(attributeStore);

            const brickFieldSelection = Ext.create('Ext.form.ComboBox', {
                displayField: 'name',
                valueField: 'key',
                queryMode: 'local',
                forceSelection: true,
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_objectBrick_field'),
                name: this.dataNamePrefix + 'brickField',
                value: this.data.brickField,
                allowBlank: false,
                msgTarget: 'under',
                listeners: {
                    change: function (combo, newValue) {
                        this.initAttributeStore(attributeStore, newValue);
                    }.bind(this)
                }
            });

            const brickFieldStore = Ext.create('Ext.data.JsonStore', {
                fields: ['key', 'name'],
                listeners: {
                    dataChanged: function (store) {
                        if (!this.brickFieldDataApplied) {
                            brickFieldSelection.setValue(this.data.brickField);
                            if (this.form) this.form.isValid();
                            this.brickFieldDataApplied = true;
                        }

                        if (!store.findRecord('key', brickFieldSelection.getValue())) {
                            brickFieldSelection.setValue(null);
                            this.form.isValid();
                        }
                    }.bind(this)
                }
            });

            brickFieldSelection.setStore(brickFieldStore);

            this.configItemRootContainer.on(
                pimcore.plugin.pimcoreDataImporterBundle.configuration.events.classChanged,
                function (combo, newValue) {
                    this.dataObjectClassId = newValue;
                    this.initBrickFieldStore(brickFieldStore);
                    this.initAttributeStore(attributeStore, brickFieldSelection.getValue());
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
                    brickFieldSelection,
                    attributeSelection,
                    {
                        xtype: 'fieldcontainer',
                        layout: 'hbox',
                        defaultType: 'checkboxfield',
                        items: [writeIfTargetIsNotEmpty, writeIfSourceIsEmpty]
                    }
                ]
            });

            this.initBrickFieldStore(brickFieldStore);
            this.initAttributeStore(attributeStore, this.data.brickField);
        }

        return this.form;
    },

    initBrickFieldStore: function (brickFieldStore) {
        const classId = this.dataObjectClassId;

        let objectBrickFieldCache = this.configItemRootContainer.objectBrickFieldCache || {};

        if (objectBrickFieldCache[classId]) {
            if (objectBrickFieldCache[classId].loading) {
                setTimeout(this.initBrickFieldStore.bind(this, brickFieldStore), 400);
            } else {
                brickFieldStore.loadData(objectBrickFieldCache[classId].data);
            }
        } else {
            objectBrickFieldCache[classId] = {
                loading: true,
                data: null
            };
            this.configItemRootContainer.objectBrickFieldCache = objectBrickFieldCache;

            Ext.Ajax.request({
                url: Routing.generate('pimcore_dataimporter_configdataobject_loadobjectbrickfieldsbyclass'),
                method: 'GET',
                params: {
                    'class_id': classId
                },
                success: function (response) {
                    let data = Ext.decode(response.responseText);

                    objectBrickFieldCache[classId].loading = false;
                    objectBrickFieldCache[classId].data = data.attributes;

                    brickFieldStore.loadData(objectBrickFieldCache[classId].data);
                }.bind(this)
            });
        }
    },

    initAttributeStore: function (attributeStore, brickField) {
        if (!brickField) {
            attributeStore.loadData([]);
            return;
        }

        Ext.Ajax.request({
            url: Routing.generate('pimcore_dataimporter_configdataobject_loadobjectbrickattributes'),
            method: 'GET',
            params: {
                'class_id': this.dataObjectClassId,
                'brick_field': brickField
            },
            success: function (response) {
                let data = Ext.decode(response.responseText);
                attributeStore.loadData(data.attributes);
            }.bind(this)
        });
    }

});

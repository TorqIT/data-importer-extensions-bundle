pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.objectBrick");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.objectBrick = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'objectBrick',
    dataApplied: false,
    dataObjectClassId: null,

    buildSettingsForm: function () {

        if (!this.form) {
            this.dataObjectClassId = this.configItemRootContainer.currentDataValues.dataObjectClassId;

            const brickFieldSelection = Ext.create('Ext.form.ComboBox', {
                displayField: 'name',
                valueField: 'key',
                queryMode: 'local',
                forceSelection: true,
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_objectBrick_field'),
                name: this.dataNamePrefix + 'fieldName',
                value: this.data.fieldName,
                allowBlank: false,
                msgTarget: 'under'
            });

            const brickFieldStore = Ext.create('Ext.data.JsonStore', {
                fields: ['key', 'name'],
                listeners: {
                    dataChanged: function (store) {
                        if (!this.dataApplied) {
                            brickFieldSelection.setValue(this.data.fieldName);
                            if (this.form) this.form.isValid();
                            this.dataApplied = true;
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
                    brickFieldSelection,
                    {
                        xtype: 'checkbox',
                        boxLabel: t('plugin_pimcore_datahub_data_importer_configpanel_objectBrick_remove_other_types'),
                        name: this.dataNamePrefix + 'removeOtherTypes',
                        value: this.data.hasOwnProperty('removeOtherTypes') ? this.data.removeOtherTypes : false,
                        inputValue: true,
                        uncheckedValue: false
                    }
                ]
            });

            this.initBrickFieldStore(brickFieldStore);
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
    }

});

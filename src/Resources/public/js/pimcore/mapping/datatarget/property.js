

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.property");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.property = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'property',
    dataApplied: false,
    dataObjectClassId: null,
    transformationResultType: null,

    buildSettingsForm: function () {

        if (!this.form) {

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
                    {
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_propertyName'),
                        name: this.dataNamePrefix + 'propertyName',
                        value: this.data.propertyName
                    }
                ]
            });
        }

        return this.form;
    },

    initAttributeStore: function (attributeStore) {
        const classId = this.dataObjectClassId;
        const transformationResultType = this.transformationResultType;

        let targetFieldCache = this.configItemRootContainer.targetFieldCache || {};

        if (targetFieldCache[classId] && targetFieldCache[classId][transformationResultType]) {

            if (targetFieldCache[classId][transformationResultType].loading) {
                setTimeout(this.initAttributeStore.bind(this, attributeStore), 400);
            } else {
                attributeStore.loadData(targetFieldCache[classId][transformationResultType].data);
            }


        } else {
            targetFieldCache = targetFieldCache || {};
            targetFieldCache[classId] = targetFieldCache[classId] || {};
            targetFieldCache[classId][transformationResultType] = {
                loading: true,
                data: null
            };
            this.configItemRootContainer.targetFieldCache = targetFieldCache;

            Ext.Ajax.request({
                url: Routing.generate('pimcore_dataimporter_configdataobject_loaddataobjectattributes'),
                method: 'GET',
                params: {
                    'class_id': classId,
                    'transformation_result_type': transformationResultType,
                    'system_write': 1
                },
                success: function (response) {
                    let data = Ext.decode(response.responseText);

                    targetFieldCache[classId][transformationResultType].loading = false;
                    targetFieldCache[classId][transformationResultType].data = data.attributes;

                    attributeStore.loadData(targetFieldCache[classId][transformationResultType].data);

                }.bind(this)
            });
        }
    },

    setLanguageVisibility: function (attributeStore, attributeSelection, languageSelection) {
        const record = attributeStore.findRecord('key', attributeSelection.getValue());
        if (record) {
            languageSelection.setHidden(!record.data.localized);
        }
    }

});
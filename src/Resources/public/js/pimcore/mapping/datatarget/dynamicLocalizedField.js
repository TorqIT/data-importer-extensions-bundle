pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.dynamicLocalizedField");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.dynamicLocalizedField = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'dynamicLocalizedField',
    dataApplied: false,
    dataObjectClassId: null,
    transformationResultType: null,

    buildSettingsForm: function () {

        if (!this.form) {
            this.dataObjectClassId = this.configItemRootContainer.currentDataValues.dataObjectClassId;
            this.transformationResultType = this.initContext.mappingConfigItemContainer.currentDataValues.transformationResultType;

            const validLanguages = pimcore.settings.websiteLanguages || [];
            const langDisplayList = validLanguages.length > 0 ? validLanguages.join(', ') : '(none configured)';

            const usageNotice = Ext.create('Ext.form.DisplayField', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dynamicLocalizedField_usage'),
                value:
                    '<b>Dynamic Localized Field</b><br>' +
                    'Map <b>two source attributes</b> to this target:<br>' +
                    '&nbsp;&nbsp;[0] <b>Value</b> &mdash; the data to write to the field<br>' +
                    '&nbsp;&nbsp;[1] <b>Language</b> &mdash; the locale code (e.g. <code>en</code>, <code>de</code>)',
                labelWidth: 120,
                width: 500
            });

            const languagesDisplay = Ext.create('Ext.form.DisplayField', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dynamicLocalizedField_valid_languages'),
                value: langDisplayList,
                labelWidth: 120,
                width: 500,
                style: 'color: #333; font-family: monospace;'
            });

            const attributeSelection = Ext.create('Ext.form.ComboBox', {
                displayField: 'title',
                valueField: 'key',
                queryMode: 'local',
                forceSelection: true,
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dynamicLocalizedField_target_field'),
                name: this.dataNamePrefix + 'fieldName',
                value: this.data.fieldName,
                allowBlank: false,
                msgTarget: 'under',
                labelWidth: 120,
                width: 500,
                listeners: {
                    errorchange: this.initContext.updateValidationStateCallback
                }
            });

            const attributeStore = Ext.create('Ext.data.JsonStore', {
                fields: ['key', 'title', 'localized'],
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
                            if (this.form) {
                                this.form.isValid();
                            }
                        }
                    }.bind(this)
                }
            });

            attributeSelection.setStore(attributeStore);

            // Listen for class or transformation type changes
            this.initContext.mappingConfigItemContainer.on(
                pimcore.plugin.pimcoreDataImporterBundle.configuration.events.transformationResultTypeChanged,
                function (newType) {
                    this.transformationResultType = newType;
                    this.initAttributeStore(attributeStore);
                }.bind(this)
            );

            this.configItemRootContainer.on(
                pimcore.plugin.pimcoreDataImporterBundle.configuration.events.classChanged,
                function (combo, newValue) {
                    this.dataObjectClassId = newValue;
                    this.initAttributeStore(attributeStore);
                }.bind(this)
            );

            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 120,
                    width: 500
                },
                border: false,
                items: [
                    usageNotice,
                    attributeSelection,
                    languagesDisplay
                ]
            });

            this.initAttributeStore(attributeStore);
        }

        return this.form;
    },

    initAttributeStore: function (attributeStore) {
        const classId = this.dataObjectClassId;

        // Always query for 'default' type fields (input, textarea, wysiwyg, etc.)
        // regardless of the pipeline's result type, since we write the value
        // slot ([0]) as a string to the localized field.
        const transformationResultType = 'default';

        const targetFieldCache = this.configItemRootContainer.dynamicLocalizedFieldCache || {};

        if (targetFieldCache[classId]) {
            if (targetFieldCache[classId].loading) {
                setTimeout(this.initAttributeStore.bind(this, attributeStore), 400);
            } else {
                attributeStore.loadData(this.filterLocalizedAttributes(targetFieldCache[classId].data));
            }
        } else {
            targetFieldCache[classId] = {
                loading: true,
                data: null
            };
            this.configItemRootContainer.dynamicLocalizedFieldCache = targetFieldCache;

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

                    targetFieldCache[classId].loading = false;
                    targetFieldCache[classId].data = data.attributes;

                    attributeStore.loadData(this.filterLocalizedAttributes(data.attributes));
                }.bind(this)
            });
        }
    },

    filterLocalizedAttributes: function (attributes) {
        if (!attributes) {
            return [];
        }
        return attributes.filter(function (attr) {
            return !!attr.localized;
        });
    }
});

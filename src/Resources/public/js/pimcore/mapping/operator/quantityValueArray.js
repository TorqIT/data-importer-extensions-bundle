pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.quantityValueArray");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.quantityValueArray = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator,
    {
        type: 'quantityValueArray',

        getMenuGroup: function () {
            return this.menuGroups.dataTypes;
        },

        getIconClass: function () {
            return 'pimcore_icon_quantityValue';
        },

        getFormItems: function () {
            this.data.settings = this.data.settings || {};

            const unitStore = Ext.create('Ext.data.JsonStore', {
                fields: ['unitId', 'abbreviation'],
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: Routing.generate('pimcore_dataimporter_configdataobject_loadunitdata'),
                    reader: {
                        type: 'json',
                        rootProperty: 'UnitList'
                    }
                }
            });

            const staticUnitSelect = Ext.create('Ext.form.ComboBox', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_quantityValue_unit_select_label'),
                name: 'settings.staticUnitSelect',
                value: this.data.settings.staticUnitSelect || null,
                displayField: 'abbreviation',
                valueField: 'unitId',
                store: unitStore,
                hidden: this.data.settings.unitSourceSelect !== 'static',
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                }
            });

            const unitSourceSelect = Ext.create('Ext.form.ComboBox', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_quantityValue_unit_source'),
                name: 'settings.unitSourceSelect',
                value: this.data.settings.unitSourceSelect || 'id',
                forceSelection: true,
                store: [
                    ['id', t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_quantityValue_unit_source_id')],
                    ['abbr', t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_quantityValue_unit_source_abbreviation')],
                    ['static', t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_quantityValue_unit_source_static')]
                ],
                listeners: {
                    change: function (combo, unitSource) {
                        staticUnitSelect.setHidden(unitSource !== 'static');
                        this.inputChangePreviewUpdate();
                        this.transformationResultTypeChangeCallback();
                    }.bind(this)
                }
            });

            const unitNullIfNoValueCheckbox = Ext.create('Ext.form.Checkbox', {
                xtype: 'checkbox',
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_quantityValue_unit_null_if_no_value'),
                allowBlank: true,
                value: this.data.settings.unitNullIfNoValueCheckbox || false,
                name: 'settings.unitNullIfNoValueCheckbox',
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                }
            });

            return [
                unitSourceSelect,
                staticUnitSelect,
                unitNullIfNoValueCheckbox
            ];
        }
    }
);

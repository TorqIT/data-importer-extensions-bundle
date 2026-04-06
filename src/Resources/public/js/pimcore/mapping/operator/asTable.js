pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asTable");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asTable = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {

    type: 'asTable',

    getMenuGroup: function () {
        return this.menuGroups.dataTypes;
    },

    getIconClass: function () {
        return "pimcore_icon_table";
    },

    getFormItems: function () {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_asTable_columnDelimiter'),
                value: this.data.settings ? this.data.settings.columnDelimiter : ',',
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                },
                name: 'settings.columnDelimiter',
                width: 400,
                labelWidth: 200
            },
            {
                xtype: 'textfield',
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_asTable_rowDelimiter'),
                value: this.data.settings ? this.data.settings.rowDelimiter : '|',
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                },
                name: 'settings.rowDelimiter',
                width: 400,
                labelWidth: 200
            }
        ];
    }
});

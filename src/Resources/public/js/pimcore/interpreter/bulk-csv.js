

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkCsv");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkCsv = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'bulkCsv',

    buildSettingsForm: function() {

        if(!this.form) {
            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 200,
                    width: 600,
                },
                border: false,
                items: [
                    {
                        xtype: 'checkbox',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_skip_first_row'),
                        name: this.dataNamePrefix + 'skipFirstRow',
                        value: this.data.hasOwnProperty('skipFirstRow') ? this.data.skipFirstRow : false,
                        inputValue: true
                    }
                ]
            });
        }

        return this.form;
    }

});
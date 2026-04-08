


pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkCsv");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkCsv = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'bulkCsv',

    buildSettingsForm: function() {

        if(!this.form) {
            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 200,
                    width: 600,
                    allowBlank: false,
                    msgTarget: 'under'
                },
                border: false,
                items: [
                    {
                        xtype: 'checkbox',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_skip_first_row'),
                        name: this.dataNamePrefix + 'skipFirstRow',
                        value: this.data.hasOwnProperty('skipFirstRow') ? this.data.skipFirstRow : false,
                        inputValue: true
                    },{
                        xtype: 'checkbox',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_save_row_header'),
                        name: this.dataNamePrefix + 'saveHeaderName',
                        value: this.data.hasOwnProperty('saveHeaderName') ? this.data.saveHeaderName : false,
                        inputValue: true
                    },{
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_delimiter'),
                        name: this.dataNamePrefix + 'delimiter',
                        value: this.data.delimiter || ',',
                        width: 250
                    },{
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_enclosure'),
                        name: this.dataNamePrefix + 'enclosure',
                        value: this.data.enclosure || '"',
                        width: 250
                    },{
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_escape'),
                        name: this.dataNamePrefix + 'escape',
                        value: this.data.escape || '\\',
                        width: 250
                    },
                ]
            });
        }

        return this.form;
    }

});

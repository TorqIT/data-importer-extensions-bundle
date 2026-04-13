

pimcore.registerNS('pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.load.advancedPath');
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.load.advancedPath = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'advancedPath',

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
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_advanced_path'),
                        name: this.dataNamePrefix + 'advancedPath',
                        value: this.data.advancedPath
                    }
                ]
            });
        }

        return this.form;
    }

});
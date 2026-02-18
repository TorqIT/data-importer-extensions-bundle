

pimcore.registerNS('pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.location.advancedParent');
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.location.advancedParent = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'advancedParent',

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
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_advanced_parent_path'),
                        name: this.dataNamePrefix + 'advancedParent',
                        value: this.data.advancedParent
                    },{
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_fallback_path'),
                        name: this.dataNamePrefix + 'fallbackPath',
                        value: this.data.fallbackPath
                    }
                ]
            });
        }

        return this.form;
    }

});
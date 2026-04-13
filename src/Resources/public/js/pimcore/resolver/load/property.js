

pimcore.registerNS('pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.load.property');
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.load.property = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'property',

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
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_property_name'),
                        name: this.dataNamePrefix + 'propertyName',
                        value: this.data.propertyName
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_value_index'),
                        name: this.dataNamePrefix + 'valueIndex',
                        value: this.data.valueIndex
                    }
                ]
            });
        }

        return this.form;
    }

});
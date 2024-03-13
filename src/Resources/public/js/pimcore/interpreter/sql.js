pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.sql");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.sql = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'sql',

    buildSettingsForm: function() {

        if(!this.form) {
            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 200,
                    width: 600
                },
                border: false,
                items: [
                ]
            });
        }

        return this.form;
    }

});
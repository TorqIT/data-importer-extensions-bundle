pimcore.registerNS(
  "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkSql"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkSql =
  Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components
      .abstractOptionType,
    {
      type: "bulkSql",

      buildSettingsForm: function () {
        if (!this.form) {
          this.form = Ext.create("DataHub.DataImporter.StructuredValueForm", {
            defaults: {
              labelWidth: 200,
              width: 600,
            },
            border: false,
            items: [],
          });
        }

        return this.form;
      },
    }
  );

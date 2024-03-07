pimcore.registerNS(
  "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.XMLSchemaBasedPreview"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.XMLSchemaBasedPreview =
  Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components
      .interpreter.xml,
    {
      type: "XMLSchemaBasedPreview",
    }
  );

pimcore.registerNS(
  "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.optionalElementsXml"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.optionalElementsXml =
  Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components
      .interpreter.xml,
    {
      type: "optionElementsXml",
    }
  );

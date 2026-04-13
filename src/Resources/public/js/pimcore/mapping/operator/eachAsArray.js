pimcore.registerNS(
    "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.eachAsArray"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.eachAsArray =
    Class.create(
        pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping
            .abstractOperator,
        {
            type: "eachAsArray",

            getMenuGroup: function () {
                return this.menuGroups.dataTypes;
            },
        }
    );

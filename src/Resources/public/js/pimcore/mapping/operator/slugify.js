pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.slugify");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.slugify = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {
        type: "slugify",
        getMenuGroup: function () {
            return this.menuGroups.dataManipulation;
        },
    }
);

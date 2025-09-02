pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.classificationStoreNamesToIds");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.classificationStoreNamesToIds = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {
        type: "classificationStoreNamesToIds",
        getMenuGroup: function () {
            return this.menuGroups.dataManipulation;
        },
        getFormItems: function () {
            return [];
        }
    }
);

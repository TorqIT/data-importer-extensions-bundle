pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.batchClassificationStoreNamesToIds");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.batchClassificationStoreNamesToIds = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {
        type: "batchClassificationStoreNamesToIds",
        getMenuGroup: function () {
            return this.menuGroups.dataManipulation;
        },
        getFormItems: function () {
            return [];
        }
    }
);

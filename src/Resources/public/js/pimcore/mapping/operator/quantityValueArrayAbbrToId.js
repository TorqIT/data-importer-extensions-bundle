pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.quantityValueArrayAbbrToId");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.quantityValueArrayAbbrToId = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {
        type: "quantityValueArrayAbbrToId",
        getMenuGroup: function () {
            return this.menuGroups.dataManipulation;
        },
        getFormItems: function () {
            return [];
        }
    }
);

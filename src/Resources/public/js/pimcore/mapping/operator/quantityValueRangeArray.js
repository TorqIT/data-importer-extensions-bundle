pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.quantityValueRangeArray");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.quantityValueRangeArray = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {
        type: "quantityValueRangeArray",
        getMenuGroup: function () {
            return this.menuGroups.dataTypes;
        },
        getIconClass: function () {
            return "pimcore_icon_inputQuantityValue";
        },
    }
);

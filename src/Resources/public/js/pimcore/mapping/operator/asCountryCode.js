pimcore.registerNS(
    "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asCountryCode"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asCountryCode =
    Class.create(
        pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping
            .abstractOperator,
        {
            type: "asCountryCode",

            getMenuGroup: function () {
                return this.menuGroups.dataTypes;
            },

            getIconClass: function () {
                return "pimcore_icon_countrymultiselect";
            },
        }
    );

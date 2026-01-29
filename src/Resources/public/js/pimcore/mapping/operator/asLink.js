pimcore.registerNS(
    "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asLink"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asLink =
    Class.create(
        pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping
            .abstractOperator,
        {
            type: "asLink",

            getMenuGroup: function () {
                return this.menuGroups.dataTypes;
            },

            getIconClass: function () {
                return "pimcore_icon_link";
            },
        }
    );

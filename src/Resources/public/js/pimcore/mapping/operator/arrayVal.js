pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.arrayVal");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.arrayVal = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {

    type: 'arrayVal',

    getMenuGroup: function() {
        return this.menuGroups.dataManipulation;
    },

    getFormItems: function() {
        return [
            {
                xtype: 'textfield',
                fieldLabel: "Array Value",
                value: this.data.settings ? this.data.settings.index : 0,
                name: 'settings.index',
            },
            {
                xtype: 'checkbox',
                fieldLabel: "Search Arrays",
                value: this.data.settings ? this.data.settings.recursiveSearch : false,
                name: 'settings.recursiveSearch',
            },
            {
                xtype: 'checkbox',
                fieldLabel: "Return null if key nonexistent",
                value: this.data.settings ? this.data.settings.returnNullIfNotFound : false,
                name: 'settings.returnNullIfNotFound',
            }
        ];
    }

});

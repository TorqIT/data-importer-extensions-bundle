pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.arithmetic");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.arithmetic = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {

    type: 'arithmetic',

    getMenuGroup: function() {
        return this.menuGroups.dataManipulation;
    },

    getFormItems: function() {
        var arithmeticOperators = Ext.create('Ext.data.Store', {
            fields: ["type"],
            data : [
                {"type": "Addition"},
                {"type": "Subtraction"},
                {"type": "Multiplication"},
                {"type": "Division"},
            ]
        });

        return [
            {
                xtype: 'combobox',
                fieldLabel: "arithmetic Operator",
                store: arithmeticOperators,
                displayField: 'type',
                valueField: 'type',
                value: this.data.settings ? this.data.settings.arithmeticOperator : 'Addition',
                name: 'settings.arithmeticOperator',
            },
            {
                xtype: 'textfield',
                fieldLabel: "Static Number",
                value: this.data.settings ? this.data.settings.staticNumber : 0,
                name: 'settings.staticNumber',
            }
        ];
    }

});
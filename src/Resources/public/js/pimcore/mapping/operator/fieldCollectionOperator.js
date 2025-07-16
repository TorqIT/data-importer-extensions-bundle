pimcore.registerNS(
    "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.fieldCollectionOperator"
);

pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.fieldCollectionOperator =
    Class.create(
        pimcore.plugin.pimcoreDataImporterBundle.configuration.components
            .mapping.abstractOperator,
        {
            type: "fieldCollectionOperator",

            sourceAttributes: null,

            // Setter to receive source attribute values as an ordered array
            setSourceAttributes: function (attributesArray) {
                this.sourceAttributes = attributesArray || [];
            },

            getMenuGroup: function () {
                return this.menuGroups.dataTypes;
            },

            getFormItems: function () {
                const items = [];

                console.log(this.data.settings);

                const fieldCollectionSelector = Ext.create(
                    "Ext.form.ComboBox",
                    {
                        fieldLabel: "Field Collection",
                        name: "settings.fieldCollectionKey",
                        store: {
                            xtype: "store",
                            fields: ["key", "name"],
                            proxy: {
                                type: "ajax",
                                url: "/load-class-fieldcollection-attributes",
                                reader: {
                                    type: "json",
                                    rootProperty: "attributes",
                                },
                            },
                            autoLoad: true,
                        },
                        displayField: "name",
                        valueField: "key",
                        value: this.data.settings?.fieldCollectionKey ?? "",
                        listeners: {
                            select: function (combo, record) {
                                this.loadFieldCollectionFields(
                                    record.get("key")
                                );
                            }.bind(this),
                            change: this.inputChangePreviewUpdate.bind(this),
                        },
                    }
                );

                this.dynamicFieldContainer = Ext.create(
                    "Ext.form.FieldContainer",
                    {
                        layout: "anchor",
                        style: {
                            marginTop: "15px",
                            padding: "10px",
                            borderTop: "1px solid #ccc",
                        },
                    }
                );

                items.push(fieldCollectionSelector);
                items.push(this.dynamicFieldContainer);

                if (this.data.settings?.fieldCollectionKey) {
                    this.loadFieldCollectionFields(
                        this.data.settings.fieldCollectionKey
                    );
                }

                return items;
            },

            loadFieldCollectionFields: function (fieldCollectionKey) {
                Ext.Ajax.request({
                    url: "/load-class-fieldcollection-fields",
                    method: "GET",
                    params: { key_id: fieldCollectionKey },
                    success: function (response) {
                        const data = Ext.decode(response.responseText);
                        if (!data.fields) return;

                        this.dynamicFieldContainer.removeAll(true); // clear old fields

                        data.fields.forEach((field) => {
                            let fieldCfg = {
                                name: "settings.fieldMappings." + field.name,
                                fieldLabel: field.title || field.name,
                                allowBlank: !field.mandatory,
                                width: 500,
                                listeners: {
                                    change: this.inputChangePreviewUpdate.bind(
                                        this
                                    ),
                                },
                            };

                            let xtypeClass;
                            switch (field.fieldtype) {
                                case "input":
                                    fieldCfg.xtype = "textfield";
                                    xtypeClass = "Ext.form.field.Text";
                                    break;

                                case "select":
                                    xtypeClass = "Ext.form.field.ComboBox";
                                    fieldCfg.xtype = "combo";
                                    fieldCfg.displayField = "value";
                                    fieldCfg.valueField = "key";
                                    fieldCfg.queryMode = "local";
                                    fieldCfg.editable = false;

                                    if (
                                        field.optionsProviderClass &&
                                        field.optionsProviderData
                                    ) {
                                        fieldCfg.store = new Ext.data.JsonStore(
                                            {
                                                proxy: {
                                                    type: "ajax",
                                                    url: Routing.generate(
                                                        "custom_select_options_provider"
                                                    ),
                                                    method: "GET",
                                                    extraParams: {
                                                        fieldType:
                                                            field.fieldtype,
                                                        providerClass:
                                                            field.optionsProviderClass,
                                                        providerData:
                                                            field.optionsProviderData,
                                                    },
                                                    reader: {
                                                        type: "json",
                                                        rootProperty: "data",
                                                    },
                                                },
                                                fields: ["key", "value"],
                                                autoLoad: true,
                                            }
                                        );
                                    } else {
                                        fieldCfg.store =
                                            new Ext.data.ArrayStore({
                                                fields: ["key", "value"],
                                                data: (field.options || []).map(
                                                    (opt) => [
                                                        opt.key,
                                                        opt.value,
                                                    ]
                                                ),
                                            });
                                    }
                                    break;

                                default:
                                    fieldCfg.xtype = "textfield";
                                    xtypeClass = "Ext.form.field.Text";
                                    break;
                            }

                            const fieldComponent = Ext.create(
                                xtypeClass,
                                fieldCfg
                            );

                            if (
                                this.sourceAttributes &&
                                this.sourceAttributes.hasOwnProperty(field.name)
                            ) {
                                fieldComponent.setValue(
                                    this.sourceAttributes[field.name]
                                );
                            }

                            this.dynamicFieldContainer.add(fieldComponent);
                        });

                        this.dynamicFieldContainer.updateLayout();
                    }.bind(this),
                });
            },

            getIconClass: function () {
                return "pimcore_nav_icon_fieldcollection";
            },
        }
    );

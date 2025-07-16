pimcore.registerNS(
    "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.fieldCollection"
);

pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.fieldCollection =
    Class.create(
        pimcore.plugin.pimcoreDataImporterBundle.configuration.components
            .abstractOptionType,
        {
            type: "fieldCollection",
            dataApplied: false,
            keyNameLoaded: false,
            dataObjectClassId: null,
            transformationResultType: null,

            buildSettingsForm: function () {
                if (!this.form) {
                    this.dataObjectClassId =
                        this.configItemRootContainer.currentDataValues.dataObjectClassId;
                    this.transformationResultType =
                        this.initContext.mappingConfigItemContainer.currentDataValues.transformationResultType;

                    let languages = [""].concat(
                        pimcore.settings.websiteLanguages
                    );

                    const languageSelection = Ext.create("Ext.form.ComboBox", {
                        store: languages,
                        forceSelection: true,
                        fieldLabel: t("language"),
                        name: this.dataNamePrefix + "language",
                        value: this.data.language,
                        allowBlank: true,
                        hidden: true,
                    });

                    const attributeSelection = Ext.create("Ext.form.ComboBox", {
                        displayField: "name",
                        valueField: "key",
                        queryMode: "local",
                        forceSelection: true,
                        fieldLabel: t(
                            "plugin_pimcore_datahub_data_importer_configpanel_fieldName"
                        ),
                        name: this.dataNamePrefix + "fieldName",
                        value: this.data.fieldName,
                        allowBlank: false,
                        msgTarget: "under",
                    });

                    const attributeStore = Ext.create("Ext.data.JsonStore", {
                        fields: ["key", "name", "localized"],
                        listeners: {
                            dataChanged: function (store) {
                                if (!this.dataApplied) {
                                    attributeSelection.setValue(
                                        this.data.fieldName
                                    );
                                    if (this.form) this.form.isValid();
                                    this.dataApplied = true;
                                    this.setLanguageVisibility(
                                        attributeStore,
                                        attributeSelection,
                                        languageSelection
                                    );
                                }

                                if (
                                    !store.findRecord(
                                        "key",
                                        attributeSelection.getValue()
                                    )
                                ) {
                                    attributeSelection.setValue(null);
                                    this.form.isValid();
                                }
                            }.bind(this),
                        },
                    });

                    attributeSelection.setStore(attributeStore);

                    this.initContext.mappingConfigItemContainer.on(
                        pimcore.plugin.pimcoreDataImporterBundle.configuration
                            .events.transformationResultTypeChanged,
                        function (newType) {
                            this.transformationResultType = newType;
                        }.bind(this)
                    );

                    this.configItemRootContainer.on(
                        pimcore.plugin.pimcoreDataImporterBundle.configuration
                            .events.classChanged,
                        function (combo, newValue) {
                            this.dataObjectClassId = newValue;
                            this.initAttributeStore(attributeStore);
                        }.bind(this)
                    );

                    this.form = Ext.create(
                        "DataHub.DataImporter.StructuredValueForm",
                        {
                            defaults: {
                                labelWidth: 120,
                                width: 500,
                                listeners: {
                                    errorchange:
                                        this.initContext
                                            .updateValidationStateCallback,
                                },
                            },
                            border: false,
                            items: [attributeSelection, languageSelection],
                        }
                    );

                    this.initAttributeStore(attributeStore);
                }

                return this.form;
            },

            initAttributeStore: function (attributeStore) {
                const classId = this.dataObjectClassId;
                // const transformationResultType = this.transformationResultType;

                let classificationStoreFieldCache =
                    this.configItemRootContainer
                        .classificationStoreFieldCache || {};

                if (classificationStoreFieldCache[classId]) {
                    if (classificationStoreFieldCache[classId].loading) {
                        setTimeout(
                            this.initAttributeStore.bind(this, attributeStore),
                            400
                        );
                    } else {
                        attributeStore.loadData(
                            classificationStoreFieldCache[classId].data
                        );
                    }
                } else {
                    classificationStoreFieldCache =
                        classificationStoreFieldCache || {};
                    classificationStoreFieldCache[classId] = {
                        loading: true,
                        data: null,
                    };
                    this.configItemRootContainer.classificationStoreFieldCache =
                        classificationStoreFieldCache;

                    Ext.Ajax.request({
                        url: Routing.generate(
                            "pimcore_dataimporter_configdataobject_loadfieldcollectionfieldsbyclass"
                        ),
                        method: "GET",
                        params: {
                            class_id: classId,
                        },
                        success: function (response) {
                            let data = Ext.decode(response.responseText);

                            classificationStoreFieldCache[
                                classId
                            ].loading = false;
                            classificationStoreFieldCache[classId].data =
                                data.attributes;

                            attributeStore.loadData(
                                classificationStoreFieldCache[classId].data
                            );
                        }.bind(this),
                    });
                }
            },

            setLanguageVisibility: function (
                attributeStore,
                attributeSelection,
                languageSelection,
                clsKeySelection
            ) {
                const record = attributeStore.findRecord(
                    "key",
                    attributeSelection.getValue()
                );
                if (record) {
                    languageSelection.setHidden(!record.data.localized);

                    if (clsKeySelection) {
                        clsKeySelection.show();
                    }
                } else if (clsKeySelection) {
                    clsKeySelection.hide();
                }
            },

            updateDataKeyLabel: function (groupName, keyName) {
                this.clsKeySelectionLabel.setValue(
                    keyName +
                        " " +
                        t(
                            "plugin_pimcore_datahub_data_importer_configpanel_classification_key_in_group"
                        ) +
                        " " +
                        groupName
                );
            },
        }
    );

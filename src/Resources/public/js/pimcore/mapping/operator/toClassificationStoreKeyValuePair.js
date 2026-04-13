Ext.util.CSS.createStyleSheet(
    '.datahub_operator_toClassificationStoreKeyValuePair {'
    + 'background-image: url(/bundles/pimcoreadmin/img/flat-color-icons/genealogy.svg) !important;'
    + 'background-size: 16px 16px;'
    + 'background-repeat: no-repeat;'
    + 'background-position: center;'
    + '}',
    'datahub_operator_toClassificationStoreKeyValuePair'
);

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.toClassificationStoreKeyValuePair");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.toClassificationStoreKeyValuePair = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator,
    {
        type: "toClassificationStoreKeyValuePair",

        getMenuGroup: function () {
            return this.menuGroups.dataManipulation;
        },

        getIconClass: function () {
            return "datahub_operator_toClassificationStoreKeyValuePair";
        },

        getFormItems: function () {
            const savedStoreId = this.data.settings && this.data.settings.storeId
                ? this.data.settings.storeId
                : null;

            const storeStore = Ext.create('Ext.data.Store', {
                fields: ['id', 'name'],
                proxy: {
                    type: 'ajax',
                    url: '/admin/classificationstore/list-stores',
                    reader: {
                        type: 'json',
                    }
                },
                autoLoad: true
            });

            return [
                {
                    xtype: 'combobox',
                    fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_classification_store'),
                    name: 'settings.storeId',
                    store: storeStore,
                    displayField: 'name',
                    valueField: 'id',
                    queryMode: 'local',
                    forceSelection: true,
                    allowBlank: true,
                    value: savedStoreId,
                    width: 400,
                    emptyText: t('plugin_pimcore_datahub_data_importer_configpanel_not_selected'),
                    listeners: {
                        change: this.inputChangePreviewUpdate.bind(this),
                    },
                }
            ];
        }
    }
);

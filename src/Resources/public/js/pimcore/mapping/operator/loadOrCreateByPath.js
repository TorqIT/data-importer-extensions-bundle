pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.loadOrCreateByPath");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.loadOrCreateByPath = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {

    type: 'loadOrCreateByPath',

    getMenuGroup: function () {
        return this.menuGroups.loadImport;
    },

    getIconClass: function () {
        return "pimcore_icon_object pimcore_icon_overlay_add";
    },

    getFormItems: function () {
        const settings = this.data.settings || {};
        const setting = function (name, fallback) {
            return Object.prototype.hasOwnProperty.call(settings, name) ? settings[name] : fallback;
        };

        const textField = function (name, labelKey, fallback) {
            return {
                xtype: 'textfield',
                fieldLabel: t(labelKey),
                name: 'settings.' + name,
                value: setting(name, fallback),
                width: 600,
                labelWidth: 200,
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                }
            };
        }.bind(this);

        // Unchecked boxes must still be submitted: several settings default to true server-side.
        const checkbox = function (name, labelKey, fallback) {
            return {
                xtype: 'checkbox',
                fieldLabel: t(labelKey),
                name: 'settings.' + name,
                value: setting(name, fallback),
                inputValue: true,
                uncheckedValue: false,
                labelWidth: 200,
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                }
            };
        }.bind(this);

        return [
            textField('pathPrefix', 'plugin_pimcore_datahub_data_importer_configpanel_loadOrCreateByPath_path_prefix', ''),
            textField('separator', 'plugin_pimcore_datahub_data_importer_configpanel_loadOrCreateByPath_separator', ' > '),
            textField('objectClass', 'plugin_pimcore_datahub_data_importer_configpanel_loadOrCreateByPath_object_class', ''),
            textField('nameField', 'plugin_pimcore_datahub_data_importer_configpanel_loadOrCreateByPath_name_field', ''),
            checkbox('createIfNotFound', 'plugin_pimcore_datahub_data_importer_configpanel_create_if_not_found', true),
            checkbox('publishOnCreate', 'plugin_pimcore_datahub_data_importer_configpanel_publish_on_create', true),
            checkbox('exactMatch', 'plugin_pimcore_datahub_data_importer_configpanel_loadOrCreateByPath_exact_match', false),
            checkbox('logNearMisses', 'plugin_pimcore_datahub_data_importer_configpanel_loadOrCreateByPath_log_near_misses', true),
            checkbox('returnAllLevels', 'plugin_pimcore_datahub_data_importer_configpanel_loadOrCreateByPath_return_all_levels', false)
        ];
    }
});

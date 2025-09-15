pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asVideo");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.asVideo = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.abstractOperator, {
        type: "asVideo",
        getMenuGroup: function () {
            return this.menuGroups.dataTypes;
        },
        getFormItems: function () {
            return [
                {
                    xtype: 'combo',
                    fieldLabel: 'Video Type',
                    value: this.data.settings ? this.data.settings.videoType : 'youtube',
                    name: 'settings.videoType',
                    listeners: {
                        change: this.inputChangePreviewUpdate.bind(this)
                    },
                    store: [
                        ['asset', 'asset'],
                        ['youtube', 'youtube'],
                        ['vimeo', 'vimeo'],
                        ['dailymotion', 'dailymotion'],
                    ],
                }
            ];
        }
    }
);

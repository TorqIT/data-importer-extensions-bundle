/**
 * Same field picker as "direct"; the write switches are hidden because merging always keeps the
 * existing values and never writes an empty source. Input must be an array of values.
 */
pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.multiselectMerge");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.multiselectMerge = Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.direct,
    {
        type: 'multiselectMerge',

        buildSettingsForm: function () {
            const firstBuild = !this.form;
            const form = pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.direct.prototype
                .buildSettingsForm.call(this);

            if (firstBuild) {
                form.items.each(function (item) {
                    if (item.xtype === 'fieldcontainer') {
                        item.hide();
                    }
                });
            }

            return form;
        }
    }
);

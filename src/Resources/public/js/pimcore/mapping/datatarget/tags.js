/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

pimcore.registerNS(
  "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.tags"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.tags =
  Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components
      .abstractOptionType,
    {
      type: "tags",
      dataApplied: false,
      dataObjectClassId: null,
      transformationResultType: null,

      buildSettingsForm: function () {
        const removeOtherTags = Ext.create("Ext.form.Checkbox", {
          boxLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dataTarget_tag_remove_other_tags'),
          name: this.dataNamePrefix + "removeOtherTags",
          value: this.data.hasOwnProperty("removeOtherTags")
            ? this.data.removeOtherTags
            : false,
          inputValue: true,
          uncheckedValue: false,
        });
        const createTagsIfNotExists = Ext.create("Ext.form.Checkbox", {
          boxLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dataTarget_tag_create_if_not_exist'),
          name: this.dataNamePrefix + "createTagsIfNotExists",
          value: this.data.hasOwnProperty("createTagsIfNotExists")
            ? this.data.createTagsIfNotExists
            : true,
          inputValue: true,
          uncheckedValue: false,
        });
        if (!this.form) {
          this.form = Ext.create("DataHub.DataImporter.StructuredValueForm", {
            defaults: {
              labelWidth: 120,
              width: 500,
              listeners: {
                errorchange: this.initContext.updateValidationStateCallback,
              },
            },
            border: false,
            items: [removeOtherTags, createTagsIfNotExists],
          });
        }

        return this.form;
      },
    }
  );

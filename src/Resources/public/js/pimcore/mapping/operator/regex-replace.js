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

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.regexReplace");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.regexReplace = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.stringReplace, {

    type: 'regexReplace',

    getFormItems: function() {
        return [
            {
                xtype: 'textfield',
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_regex_pattern'),
                value: this.data.settings ? this.data.settings.search : '',
                name: 'settings.search',
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                }
            },

            {
                xtype: 'textfield',
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_regex_replace'),
                value: this.data.settings ? this.data.settings.replace : '',
                name: 'settings.replace',
                listeners: {
                    change: this.inputChangePreviewUpdate.bind(this)
                }
            },
        ];
    }

});
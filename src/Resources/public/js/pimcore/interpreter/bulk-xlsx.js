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

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkXlsx");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.bulkXlsx = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'bulkXlsx',

    buildSettingsForm: function() {

        if(!this.form) {
            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 200,
                    width: 600,
                },
                border: false,
                items: [
                    {
                        xtype: 'checkbox',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_skip_first_row'),
                        name: this.dataNamePrefix + 'skipFirstRow',
                        value: this.data.hasOwnProperty('skipFirstRow') ? this.data.skipFirstRow : false,
                        inputValue: true
                    },{
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_xlsx_sheet'),
                        name: this.dataNamePrefix + 'sheetName',
                        value: this.data.sheetName || 'Sheet1'
                    },{
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_advanced_xlsx_unique_columns'),
                        name: this.dataNamePrefix + 'uniqueColumns',
                        value: this.data.uniqueColumns || ''
                    },{
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_advanced_xlsx_row_filter'),
                        name: this.dataNamePrefix + 'rowFilter',
                        value: this.data.rowFilter || ''
                    },{
                        xtype: 'checkbox',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_low_memory_reader'),
                        name: this.dataNamePrefix + 'lowMemoryReader',
                        value: this.data.hasOwnProperty('lowMemoryReader') ? this.data.lowMemoryReader : false,
                        inputValue: true
                    }
                ]
            });
        }

        return this.form;
    }

});
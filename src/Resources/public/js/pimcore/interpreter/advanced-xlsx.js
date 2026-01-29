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

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.advancedXlsx");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.advancedXlsx = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'advancedXlsx',

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
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_xlsx_sheet'),
                        name: this.dataNamePrefix + 'sheetName',
                        value: this.data.sheetName || 'Sheet1'
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_bulk_xlsx_header_row'),
                        name: this.dataNamePrefix + 'headerRow',
                        value: this.data.hasOwnProperty('headerRow') ? this.data.headerRow : 1,
                        minValue: 1,
                        allowDecimals: false,
                        allowBlank: false
                    },
                    {
                        xtype: 'checkbox',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_csv_save_row_header'),
                        name: this.dataNamePrefix + 'saveHeaderName',
                        value: this.data.hasOwnProperty('saveHeaderName') ? this.data.saveHeaderName : false,
                        inputValue: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_advanced_xlsx_unique_columns'),
                        name: this.dataNamePrefix + 'uniqueColumns',
                        value: this.data.uniqueColumns || ''
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_advanced_xlsx_row_filter'),
                        name: this.dataNamePrefix + 'rowFilter',
                        value: this.data.rowFilter || ''
                    }
                ]
            });
        }

        return this.form;
    }

});
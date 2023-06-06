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

pimcore.registerNS('pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.aces');
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.interpreter.aces = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'aces',

    buildSettingsForm: function() {

        if(!this.form) {
            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 200,
                    width: 600
                },
                border: false,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_xml_xpath'),
                        name: this.dataNamePrefix + 'xpath',
                        value: this.data.xpath || '/root/item',
                        allowBlank: false,
                        msgTarget: 'under'
                    },{
                        xtype: 'textarea',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_xml_schema'),
                        name: this.dataNamePrefix + 'schema',
                        value: this.data.schema || '',
                        grow: true,
                        width: 900,
                        scrollable: true
                    }

                ]
            });
        }

        return this.form;
    }

});
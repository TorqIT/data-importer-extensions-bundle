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

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.loader.sql");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.loader.sql = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'sql',

    buildSettingsForm: function() {

        if(!this.form) {

            var dataStore = Ext.create('Ext.data.Store', {
                autoLoad: true,
                fields: ['key1', 'key2'],
                proxy: {
                    type: 'ajax',
                    url: '/admin/torqitpimcoredataimporter/getSqlConnections',
                    reader: {
                        type: 'json'
                    }
                }
            });

            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 200,
                    width: 600
                },
                border: false,
                items: [
                    {
                        xtype: 'combobox',
                        name: this.dataNamePrefix + 'connectionName',
                        itemId: 'combo1',
                        value: this.data.connectionName,
                        fieldLabel: 'Connection Name',
                        displayField: 'name',
                        valueField: 'value',
                        store: dataStore,
                        listeners: {
                            expand: function(comboBox) {
                                console.log(comboBox.getStore().getRange());
                            }
                        }
                    },
                    {
                        xtype: 'textareafield',
                        fieldLabel: 'SQL',
                        name: this.dataNamePrefix + 'sql',
                        value: this.data.sql,
                        allowBlank: false,
                        msgTarget: 'under',
                        scrollable: true,
                        maxRows: 20,
                        height:500

                    }
                ]
            });
        }

        return this.form;
    }

});
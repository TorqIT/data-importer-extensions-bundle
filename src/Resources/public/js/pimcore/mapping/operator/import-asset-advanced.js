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

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.importAssetAdvanced");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.importAssetAdvanced = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.importAsset, {

    //TODO
    type: 'importAssetAdvanced',
    getFormItems: function() {

        var formItems = this.__proto__.__proto__.getFormItems.call(this);

        var path = Ext.create('Ext.form.TextField', {
            name: 'settings.path',
            value: this.data.settings ? this.data.settings.path : '/',
        });

        var urlProperty = Ext.create('Ext.form.TextField', {
            name: 'settings.urlPropertyName',
            value: this.data.urlPropertyName ? this.data.settings.urlPropertyName : '',
        });

        let pathContainer = Ext.create('Ext.form.FieldContainer', {
            fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_path'),
            layout: 'hbox',
            items: [
                path
            ],
            width: 900,
            componentCls: "object_field object_field_type_manyToOneRelation",
            border: false,
            style: {
                padding: 0
            }
        });

        let urlContainer = Ext.create('Ext.form.FieldContainer', {
            fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_transformation_pipeline_url_property'),
            layout: 'hbox',
            items: [
                urlProperty
            ],
            width: 900,
            componentCls: "object_field object_field_type_manyToOneRelation",
            border: false,
            style: {
                padding: 0
            }
        });

        formItems.splice(0, 1, pathContainer, urlContainer);

        return formItems;
    }

});
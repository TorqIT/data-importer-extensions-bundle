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

pimcore.registerNS("pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.advancedClassificationStore");
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.advancedClassificationStore = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.datatarget.classificationstore, {

    type: 'advancedClassificationStore',
    buildSettingsForm: function() {

        var parentForm = this.__proto__.__proto__.buildSettingsForm.call(this);

        const writeIfTargetIsNotEmpty = Ext.create('Ext.form.Checkbox', {
            boxLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dataTarget.type_advancedClassificationStore_write_settings_ifTargetIsNotEmpty'),
            name: this.dataNamePrefix + 'writeIfTargetIsNotEmpty',
            value: this.data.hasOwnProperty('writeIfTargetIsNotEmpty') ? this.data.writeIfTargetIsNotEmpty : true,
            inputValue: true,
            uncheckedValue: false,
            listeners: {
                change: function (checkbox, value) {
                    if (value) {
                        writeIfSourceIsEmpty.setReadOnly(false);
                        writeIfSourceIsEmpty.setValue(true);
                    } else {
                        writeIfSourceIsEmpty.setValue(false);
                        writeIfSourceIsEmpty.setReadOnly(true);
                    }
                }
            }
        });

        const writeIfSourceIsEmpty = Ext.create('Ext.form.Checkbox', {
            boxLabel: t('plugin_pimcore_datahub_data_importer_configpanel_dataTarget.type_advancedClassificationStore_write_settings_ifSourceIsEmpty'),
            name: this.dataNamePrefix + 'writeIfSourceIsEmpty',
            value: this.data.hasOwnProperty('writeIfSourceIsEmpty') ? this.data.writeIfSourceIsEmpty : true,
            uncheckedValue: false,
            readOnly: this.data.hasOwnProperty('writeIfTargetIsNotEmpty') ? !this.data.writeIfTargetIsNotEmpty : false,
            inputValue: true
        });

        parentForm.items.add(writeIfTargetIsNotEmpty);
        parentForm.items.add(writeIfSourceIsEmpty);

        return parentForm;
    }
    
});
(function () {
    const originalGetFormItems = pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.loadDataObject.prototype.getFormItems;

    pimcore.plugin.pimcoreDataImporterBundle.configuration.components.mapping.operator.loadDataObject.addMethods({
        getFormItems: function () {
            const parentItems = originalGetFormItems.call(this);

            this.data.settings = this.data.settings || {};

            const createPathField = Ext.create('Ext.form.field.Text', {
                name: 'settings.createPath',
                value: this.data.settings.createPath || '/',
                fieldCls: 'pimcore_droptarget_input',
                width: 500,
                enableKeyEvents: true,
                allowBlank: false,
                msgTarget: 'under',
                listeners: {
                    render: function (el) {
                        new Ext.dd.DropZone(el.getEl(), {
                            reference: this,
                            ddGroup: "element",
                            getTargetFromEvent: function () {
                                return createPathField.getEl();
                            },
                            onNodeOver: function (target, dd, e, data) {
                                if (data.records.length === 1 && data.records[0].data.elementType === 'object' && data.records[0].data.type === 'folder') {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                }
                            },
                            onNodeDrop: function (target, dd, e, data) {
                                if (!pimcore.helpers.dragAndDropValidateSingleItem(data)) {
                                    return false;
                                }
                                const record = data.records[0].data;
                                if (record.elementType === 'object' && record.type === 'folder') {
                                    createPathField.setValue(record.path);
                                    return true;
                                }
                                return false;
                            }
                        });
                    }.bind(this),
                    change: this.inputChangePreviewUpdate.bind(this)
                }
            });

            const createPathContainer = Ext.create('Ext.form.FieldContainer', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_create_path'),
                layout: 'hbox',
                hidden: !this.data.settings.createIfNotFound,
                items: [
                    createPathField,
                    {
                        xtype: 'button',
                        iconCls: 'pimcore_icon_delete',
                        style: 'margin-left: 5px',
                        handler: function () {
                            createPathField.setValue('');
                        }
                    },
                    {
                        xtype: 'button',
                        iconCls: 'pimcore_icon_search',
                        style: 'margin-left: 5px',
                        handler: function () {
                            pimcore.helpers.itemselector(false, function (data) {
                                createPathField.setValue(data.fullpath);
                            }, {
                                type: ['object'],
                                subtype: { object: ['folder'] },
                                specific: {}
                            }, {});
                        }
                    }
                ],
                width: 900,
                componentCls: 'object_field object_field_type_manyToOneRelation',
                border: false,
                style: { padding: 0 }
            });

            const publishOnCreate = Ext.create('Ext.form.field.Checkbox', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_publish_on_create'),
                name: 'settings.publishOnCreate',
                allowBlank: true,
                value: this.data.settings.publishOnCreate || false,
                hidden: !this.data.settings.createIfNotFound
            });

            // Find the loadUnpublished checkbox from parent items
            let loadUnpublishedCheckbox = null;
            for (let i = 0; i < parentItems.length; i++) {
                if (parentItems[i].name === 'settings.loadUnpublished') {
                    loadUnpublishedCheckbox = parentItems[i];
                    break;
                }
            }

            const createIfNotFound = Ext.create('Ext.form.field.Checkbox', {
                fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_create_if_not_found'),
                name: 'settings.createIfNotFound',
                allowBlank: true,
                value: this.data.settings.createIfNotFound || false,
                listeners: {
                    change: function (cb, checked) {
                        createPathContainer.setHidden(!checked);
                        publishOnCreate.setHidden(!checked);
                        if (loadUnpublishedCheckbox) {
                            if (checked) {
                                loadUnpublishedCheckbox.setValue(true);
                                loadUnpublishedCheckbox.setDisabled(true);
                            } else {
                                loadUnpublishedCheckbox.setDisabled(false);
                            }
                        }
                        this.inputChangePreviewUpdate();
                    }.bind(this)
                }
            });

            // Apply initial state
            if (this.data.settings.createIfNotFound && loadUnpublishedCheckbox) {
                loadUnpublishedCheckbox.setValue(true);
                loadUnpublishedCheckbox.setDisabled(true);
            }

            parentItems.push(createIfNotFound);
            parentItems.push(createPathContainer);
            parentItems.push(publishOnCreate);

            return parentItems;
        }
    });
})();

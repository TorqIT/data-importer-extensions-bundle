pimcore.registerNS('pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.load.load_by_key');
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.resolver.load.load_by_key = Class.create(pimcore.plugin.pimcoreDataImporterBundle.configuration.components.abstractOptionType, {

    type: 'load_by_key',

    buildSettingsForm: function() {

        if (!this.form) {
            var searchPathField = Ext.create('Ext.form.TextField', {
                name: this.dataNamePrefix + 'searchPath',
                value: this.data.searchPath || '',
                fieldCls: 'pimcore_droptarget_input',
                width: 500,
                enableKeyEvents: true,
                allowBlank: true,
                msgTarget: 'under',
                emptyText: '(no restriction)',
                listeners: {
                    render: function (el) {
                        new Ext.dd.DropZone(el.getEl(), {
                            reference: searchPathField,
                            ddGroup: 'element',
                            getTargetFromEvent: function (e) {
                                return this.reference.getEl();
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
                                var record = data.records[0].data;
                                if (record.elementType === 'object' && record.type === 'folder') {
                                    searchPathField.setValue(record.path);
                                    return true;
                                }
                                return false;
                            }
                        });

                        el.getEl().on('contextmenu', function (e) {
                            var menu = new Ext.menu.Menu();
                            menu.add(new Ext.menu.Item({
                                text: t('empty'),
                                iconCls: 'pimcore_icon_delete',
                                handler: function (item) {
                                    item.parentMenu.destroy();
                                    searchPathField.setValue('');
                                }
                            }));
                            menu.add(new Ext.menu.Item({
                                text: t('search'),
                                iconCls: 'pimcore_icon_search',
                                handler: function (item) {
                                    item.parentMenu.destroy();
                                    pimcore.helpers.itemselector(false, function (data) {
                                        searchPathField.setValue(data.fullpath);
                                    }, {type: ['object'], subtype: {object: ['folder']}, specific: {}}, {});
                                }
                            }));
                            menu.showAt(e.getXY());
                            e.stopEvent();
                        });
                    }
                }
            });

            this.form = Ext.create('DataHub.DataImporter.StructuredValueForm', {
                defaults: {
                    labelWidth: 200,
                    width: 600,
                    allowBlank: false,
                    msgTarget: 'under'
                },
                border: false,
                items: [
                    {
                        xtype: 'combo',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_data_source_index'),
                        name: this.dataNamePrefix + 'dataSourceIndex',
                        value: this.data.dataSourceIndex,
                        store: this.configItemRootContainer.columnHeaderStore,
                        displayField: 'label',
                        valueField: 'dataIndex',
                        forceSelection: false,
                        queryMode: 'local',
                        triggerOnClick: false
                    },
                    {
                        xtype: 'fieldcontainer',
                        fieldLabel: 'Search Path',
                        layout: 'hbox',
                        items: [
                            searchPathField,
                            {
                                xtype: 'button',
                                iconCls: 'pimcore_icon_delete',
                                style: 'margin-left: 5px',
                                handler: function () {
                                    searchPathField.setValue('');
                                }
                            },
                            {
                                xtype: 'button',
                                iconCls: 'pimcore_icon_search',
                                style: 'margin-left: 5px',
                                handler: function () {
                                    pimcore.helpers.itemselector(false, function (data) {
                                        searchPathField.setValue(data.fullpath);
                                    }, {type: ['object'], subtype: {object: ['folder']}, specific: {}}, {});
                                }
                            }
                        ],
                        width: 900,
                        componentCls: 'object_field object_field_type_manyToOneRelation',
                        border: false,
                        style: {padding: 0}
                    },
                    {
                        xtype: 'checkbox',
                        fieldLabel: t('plugin_pimcore_datahub_data_importer_configpanel_include_unpublished'),
                        name: this.dataNamePrefix + 'includeUnpublished',
                        value: this.data.hasOwnProperty('includeUnpublished') ? this.data.includeUnpublished : false,
                        inputValue: true
                    }
                ]
            });
        }

        return this.form;
    }

});
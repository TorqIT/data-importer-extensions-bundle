pimcore.registerNS(
  "pimcore.plugin.pimcoreDataImporterBundle.configuration.components.loader.bulkSql"
);
pimcore.plugin.pimcoreDataImporterBundle.configuration.components.loader.bulkSql =
  Class.create(
    pimcore.plugin.pimcoreDataImporterBundle.configuration.components
      .abstractOptionType,
    {
      type: "bulkSql",

      buildSettingsForm: function () {
        if (!this.form) {
          const dataStore = Ext.create("Ext.data.Store", {
            autoLoad: true,
            proxy: {
              type: "ajax",
              url: "/admin/pimcoredataimporter/get-connections",
              reader: {
                type: "json",
              },
            },
          });

          this.form = Ext.create("DataHub.DataImporter.StructuredValueForm", {
            defaults: {
              labelWidth: 200,
              width: 600,
            },
            border: false,
            items: [
              {
                xtype: "combobox",
                fieldLabel: t(
                  "plugin_pimcore_datahub_data_importer_configpanel_bulkSql_connection"
                ),
                name: this.dataNamePrefix + "connection",
                value: this.data.connection,
                allowBlank: false,
                msgTarget: "under",
                displayField: "name",
                valueField: "value",
                store: dataStore,
              },
              {
                xtype: "textarea",
                fieldLabel: "SELECT <br /><small>(eg. a,b,c)*</small>",
                name: this.dataNamePrefix + "select",
                value: this.data.select,
                msgTarget: "under",
                width: 900,
                height: 200,
                grow: true,
                growMax: 400,
                allowBlank: false,
              },
              {
                xtype: "textarea",
                fieldLabel:
                  "FROM <br /><small>(eg. d INNER JOIN e ON c.a = e.b)*</small>",
                name: this.dataNamePrefix + "from",
                value: this.data.from,
                msgTarget: "under",
                width: 900,
                height: 200,
                grow: true,
                growMax: 400,
                allowBlank: false,
              },
              {
                xtype: "textarea",
                fieldLabel: "WHERE <br /><small>(eg. c = 'some_value')</small>",
                name: this.dataNamePrefix + "where",
                value: this.data.where,
                msgTarget: "under",
                width: 900,
                height: 200,
                grow: true,
                growMax: 400,
              },
              {
                xtype: "textarea",
                fieldLabel: "GROUP BY <br /><small>(eg. b, c )</small>",
                name: this.dataNamePrefix + "groupBy",
                value: this.data.groupBy,
                msgTarget: "under",
                width: 900,
                height: 200,
                grow: true,
                growMax: 400,
              },
              {
                xtype: "textfield",
                fieldLabel: "LIMIT",
                name: this.dataNamePrefix + "limit",
                value: this.data.limit,
                msgTarget: "under",
                maskRe: /[0-9]/,
              },
            ],
          });
        }

        return this.form;
      },
    }
  );

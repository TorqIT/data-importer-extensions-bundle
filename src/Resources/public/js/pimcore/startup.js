pimcore.registerNS("pimcore.plugin.TorqITDataImporterExtensionsBundle");

pimcore.plugin.TorqITDataImporterExtensionsBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.TorqITDataImporterExtensionsBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("TorqITDataImporterExtensionsBundle ready!");
    }
});

var TorqITDataImporterExtensionsBundlePlugin = new pimcore.plugin.TorqITDataImporterExtensionsBundle();

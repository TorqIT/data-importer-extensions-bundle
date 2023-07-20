<?php

namespace TorqIT\DataImporterExtensionsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class TorqITDataImporterExtensionsBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/torqitdataimporterextensions/js/pimcore/startup.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/resolver/load/advanced-path.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/resolver/load/property.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/resolver/location/advanced-parent.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/interpreter/advanced-xlsx.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/interpreter/bulk-xlsx.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/operator/safe-key.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/operator/constant.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/datatarget/property.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/datatarget/advanced-classification-store.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/operator/import-asset-advanced.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/datatarget/image-gallery-appender.js',

        ];
    }
}
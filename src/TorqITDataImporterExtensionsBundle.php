<?php

namespace TorqIT\DataImporterExtensionsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;

class TorqITDataImporterExtensionsBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface
{

    public function getAdminIframePath() { }

    public function getCssPaths(): array {  return []; }

    public function getEditmodeJsPaths(): array { return []; }

    public function getEditmodeCssPaths(): array { return []; }
   
    public function getJsPaths() : array
    {
        return [
            '/bundles/torqitdataimporterextensions/js/pimcore/startup.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/resolver/load/advanced-path.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/resolver/load/property.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/resolver/location/advanced-parent.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/interpreter/advanced-xlsx.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/interpreter/bulk-xlsx.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/interpreter/optional-elements-xml.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/operator/safe-key.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/operator/constant.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/datatarget/property.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/datatarget/advanced-classification-store.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/operator/import-asset-advanced.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/mapping/datatarget/image-gallery-appender.js',
            '/bundles/torqitdataimporterextensions/js/pimcore/loader/sql.js',

        ];
    }
}
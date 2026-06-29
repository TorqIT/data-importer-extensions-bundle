<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Registers controller routes under the prefix(es) appropriate for the installed
 * Pimcore stack, determined at cache-warmup time:
 *
 *  Scenario 1 — 2025.4, no studio:
 *    /admin only  (pimcore_admin firewall)
 *
 *  Scenario 2 — 2025.4 + studio:
 *    /admin  (pimcore_admin firewall)
 *    /pimcore-studio/api  (pimcore_studio firewall)
 *
 *  Scenario 3 — 2026.1+:
 *    /pimcore-studio/api only  (pimcore_studio firewall)
 *
 * The admin import retains original route names (required by FOSJsRouting / ExtJS).
 * The studio import uses a name prefix to avoid collisions when both are registered.
 */
return static function (RoutingConfigurator $routes): void {
    $dataImporterVersion = InstalledVersions::getVersion('pimcore/data-importer');
    if ($dataImporterVersion === null) {
        throw new RuntimeException(
            'pimcore/data-importer must be installed with a resolved version to use TorqIT DataImporterExtensionsBundle.',
        );
    }

    $isV2026 = (int)strstr($dataImporterVersion, '.', true) >= 2026;
    $hasStudio = $isV2026 || InstalledVersions::isInstalled('pimcore/studio-backend-bundle');
    if (!$isV2026) {
        $routes->import(__DIR__ . '/../../../Controller/', 'attribute')->prefix('/admin');
    }
    if ($hasStudio) {
        $import = $routes->import(__DIR__ . '/../../../Controller/', 'attribute')->prefix('/pimcore-studio/api');

        // Only prefix names when admin routes are registered to avoid collisions
        if (!$isV2026) {
            $import->namePrefix('studio_');
        }
    }
};

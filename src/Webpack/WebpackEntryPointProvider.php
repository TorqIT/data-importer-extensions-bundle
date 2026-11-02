<?php declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Webpack;

use Pimcore\Bundle\StudioUiBundle\Webpack\WebpackEntryPointProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore_studio_ui.webpack_entry_point_provider')]
final class WebpackEntryPointProvider implements WebpackEntryPointProviderInterface
{
    public function getEntryPointsJsonLocations(): array
    {
        $productionEntrypoint = __DIR__ . '/../Resources/public/build/production/entrypoints.json';
        $developmentEntrypoint = __DIR__ . '/../Resources/public/build/development/entrypoints.json';
        if (file_exists($developmentEntrypoint)) {
            return [$productionEntrypoint, $developmentEntrypoint];
        }

        return [$productionEntrypoint];
    }

    public function getEntryPoints(): array
    {
        return ['exposeRemote'];
    }

    public function getOptionalEntryPoints(): array
    {
        return [];
    }
}

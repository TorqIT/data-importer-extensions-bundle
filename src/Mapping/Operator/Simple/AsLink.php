<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Exception;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Model\DataObject\Data\Link;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'pimcore.datahub.data_importer.operator', attributes: ['type' => 'asLink'])]
class AsLink extends AbstractOperator
{
    /** @throws Exception */
    public function process($inputData, bool $dryRun = false)
    {
        $link = new Link();

        if ($inputData) {
            $link->setPath($inputData);
        }

        return $link;
    }

    /** @return mixed|string */
    public function generateResultPreview($inputData)
    {
        if ($inputData instanceof Link) {
            return $inputData->__toString();
        }
        return $inputData;
    }

    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        return 'link';
    }
}

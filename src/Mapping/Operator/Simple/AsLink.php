<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Model\DataObject\Data\Link;

class AsLink extends AbstractOperator
{
    /**
     * @throws \Exception
     */
    public function process($inputData, bool $dryRun = false)
    {
        $link = new Link();

        if ($inputData) {
            $link->setPath($inputData);
        }

        return $link;
    }

    /**
     * @param mixed $inputData
     *
     * @return mixed|string
     */
    public function generateResultPreview($inputData)
    {
        if ($inputData instanceof Link) {
            return $inputData->__toString();
        }

        return $inputData;
    }

    /**
     * @param string $inputType
     * @param int|null $index
     *
     * @return string
     */
    public function evaluateReturnType(string $inputType, int $index = null): string
    {
        return 'link';
    }
}

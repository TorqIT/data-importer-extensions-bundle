<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Type;

use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService as BaseTransformationDataTypeService;

class TransformationDataTypeService extends BaseTransformationDataTypeService
{
    public function __construct()
    {
        $this->appendTypeMapping('link', 'link');
    }
}

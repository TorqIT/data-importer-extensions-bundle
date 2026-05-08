<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Type;

use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService as BaseTransformationDataTypeService;

// FIXME: BaseTransformationDataTypeService is now final, cannot extend
class TransformationDataTypeService
{
      // FIXME: __construct is breaking container creation
    public function __construct()
    {
//        $this->appendTypeMapping('link', 'link');
//        $this->appendTypeMapping('table', 'table');
//        $this->appendTypeMapping('structuredTable', 'table');
    }
}

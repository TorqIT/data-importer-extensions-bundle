<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Type;

use TorqIT\DataImporterExtensionsBundle\Override\CustomTransformationDataTypeService;

class TransformationDataTypeService extends CustomTransformationDataTypeService
{
    public function __construct()
    {
        $this->appendTypeMapping('link', 'link');
        $this->appendTypeMapping('table', 'table');
        $this->appendTypeMapping('structuredTable', 'table');
    }
}

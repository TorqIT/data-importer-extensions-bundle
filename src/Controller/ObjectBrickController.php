<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks;
use Pimcore\Model\DataObject\Objectbrick\Definition;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/pimcoredataimporter/dataobject/config')]
#[IsGranted('plugin_datahub_adapter_dataImporterDataObject')]
class ObjectBrickController
{
    #[Route(
        '/load-class-objectbrick-fields',
        'pimcore_dataimporter_configdataobject_loadobjectbrickfieldsbyclass',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function loadObjectBrickFieldsByClassAction(Request $request): JsonResponse
    {
        $class = ClassDefinition::getById((string) $request->query->get('class_id'));

        $attributes = [];
        if ($class !== null) {
            foreach ($class->getFieldDefinitions() as $fieldDefinition) {
                if ($fieldDefinition instanceof Objectbricks) {
                    $allowedTypes = implode(', ', $fieldDefinition->getAllowedTypes());
                    $attributes[] = [
                        'key' => $fieldDefinition->getName(),
                        'name' => $fieldDefinition->getName() . ' (' . $allowedTypes . ')',
                        'localized' => false,
                    ];
                }
            }
        }

        return new JsonResponse(['attributes' => $attributes]);
    }

    #[Route(
        '/load-class-objectbrick-attributes',
        'pimcore_dataimporter_configdataobject_loadobjectbrickattributes',
        options: ['expose' => true],
        methods: ['GET'],
    )]
    public function loadObjectBrickAttributesAction(Request $request): JsonResponse
    {
        $class = ClassDefinition::getById((string) $request->query->get('class_id'));
        $brickField = (string) $request->query->get('brick_field');

        $attributes = [];
        if ($class !== null) {
            $fieldDefinition = $class->getFieldDefinition($brickField);
            if ($fieldDefinition instanceof Objectbricks) {
                $attributeBrickTypes = [];
                foreach ($fieldDefinition->getAllowedTypes() as $brickType) {
                    $brickDefinition = Definition::getByKey($brickType);
                    if ($brickDefinition === null) {
                        continue;
                    }

                    foreach ($brickDefinition->getFieldDefinitions() as $brickFieldDefinition) {
                        $attributeBrickTypes[$brickFieldDefinition->getName()][] = $brickType;
                    }
                }

                foreach ($attributeBrickTypes as $name => $brickTypes) {
                    $attributes[] = [
                        'key' => $name,
                        'name' => $name . ' (' . implode(', ', $brickTypes) . ')',
                        'localized' => false,
                    ];
                }
            }
        }

        return new JsonResponse(['attributes' => $attributes]);
    }
}

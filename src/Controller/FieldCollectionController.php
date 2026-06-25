<?php

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/pimcoredataimporter/dataobject/config')]
class FieldCollectionController
{
    #[Route('/load-class-fieldcollection-attributes', options: ['expose' => true], methods: ['GET'])]
    public function loadDataObjectFieldCollectionAttributesAction(): JsonResponse
    {
        $definitions = new Definition\Listing();
        $definitionNames = $definitions->loadNames();

        $attributes = [];
        foreach ($definitionNames as $definitionName) {
            $attributes[] = [
                'key' => $definitionName,
                'name' => $definitionName,
                'localized' => false,
            ];
        }

        return new JsonResponse(['attributes' => $attributes]);
    }

    #[Route('/load-class-fieldcollection-fields', options: ['expose' => true], methods: ['GET'])]
    public function loadDataObjectFieldCollectionFieldsAction(Request $request): JsonResponse
    {
        $key = $request->query->get('key_id');

        $fieldCollection = Definition::getByKey($key);

        $fields = [];
        foreach ($fieldCollection->getFieldDefinitions() as $definition) {
            $fields[] = $definition;
        }

        return new JsonResponse(['fields' => $fields]);
    }

    #[Route('/load-class-fieldcollection-fields-by-product', options: ['expose' => true], methods: ['GET'])]
    public function loadFieldCollectionFieldsByClassAction(Request $request): JsonResponse
    {
        $fieldDefinitions = ClassDefinition::getById($request->query->get('class_id'))->getFieldDefinitions();

        $attributes = [];
        foreach ($fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition instanceof Fieldcollections) {
                $allowedTypes = implode(', ', $fieldDefinition->getAllowedTypes());
                $attributes[] = [
                    'key' => $fieldDefinition->getName(),
                    'name' => $fieldDefinition->getName() . '(' . $allowedTypes . ')',
                    'localized' => false,
                ];
            }
        }

        return new JsonResponse(['attributes' => $attributes]);
    }
}

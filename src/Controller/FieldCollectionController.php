<?php

namespace TorqIT\DataImporterExtensionsBundle\Controller;

use Pimcore\Bundle\DataImporterBundle\Controller\ConfigDataObjectController;
use Pimcore\Bundle\DataImporterBundle\Mapping\Type\TransformationDataTypeService;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Model\DataObject\Fieldcollection\Definition;

class FieldCollectionController extends ConfigDataObjectController
{
    #[Route(
        path: '/load-class-fieldcollection-attributes',
        name: 'pimcore_dataimporter_configdataobject_loaddataobjectfieldcollectionattributes',
        methods: ['GET'],
        options: ['expose' => true]
    )]
    public function loadDataObjectFieldCollectionAttributesAction(Request $request, TransformationDataTypeService $transformationDataTypeService): JsonResponse
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

    #[Route(
        path: '/load-class-fieldcollection-fields',
        name: 'pimcore_dataimporter_configdataobject_loaddataobjectfieldcollectionfields',
        methods: ['GET'],
        options: ['expose' => true]
    )]
    public function loadDataObjectFieldCollectionFieldsAction(Request $request, TransformationDataTypeService $transformationDataTypeService): JsonResponse
    {
        $key = $request->query->get('key_id');

        $fieldCollection = Definition::getByKey($key);

        $fields = [];
        foreach ($fieldCollection->getFieldDefinitions() as $definition) {
            $fields[] = $definition;
        }

        return new JsonResponse(['fields' => $fields]);
    }

    #[Route(
        path: '/load-class-fieldcollection-fields-by-product',
        name: 'pimcore_dataimporter_configdataobject_loadfieldcollectionfieldsbyclass',
        methods: ['GET'],
        options: ['expose' => true]
    )]
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

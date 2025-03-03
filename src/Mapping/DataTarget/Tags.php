<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\DataTarget;

use Pimcore\Model\Element\ElementInterface;
use Pimcore\Bundle\DataImporterBundle\Mapping\DataTarget\DataTargetInterface;
use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Model\Element\Tag;

class Tags implements DataTargetInterface
{
    private bool $removeOtherTags;
    private bool $createTagsIfNotExists;

    public function assignData(ElementInterface $element, $data)
    {
        $tagArray = explode(',', $data);
        $tags = [];
        foreach ($tagArray ?? [] as $tag) {
            if (!$tagObj = Tag::getByPath(trim($tag))) {
                if (!$this->createTagsIfNotExists) {
                    continue;
                }
                $tagObj = $this->buildTags(explode('/', trim($tag)));
            }
            $tags[] = $tagObj;
        }

        if ($this->removeOtherTags) {
            Tag::setTagsForElement($element->getType(), $element->getId(), $tags);
        } else {
            foreach ($tags as $tag) {
                $tag->addTagToElement($element->getType(), $element->getId(), $tag);
            }
        }
    }

    public function setSettings(array $settings): void
    {
        $this->removeOtherTags = isset($settings['removeOtherTags']) ? $settings['removeOtherTags'] : false;
        $this->createTagsIfNotExists = isset($settings['createTagsIfNotExists']) ? $settings['createTagsIfNotExists'] : true;
    }

    private function buildTags(array $tags): Tag
    {
        // Loop through each part of the path and create tags if they don't exist
        $currentPath = '';
        $currentParentTag = null;
        foreach ($tags as $tagName) {
            $currentPath .= '/' . $tagName;
            // Check if the tag already exists
            if (!$tag = Tag::getByPath($currentPath)) {
                // Create the tag
                $tag = new Tag();
                $tag->setName($tagName);
                if ($currentParentTag) {
                    $tag->setParentId($currentParentTag->getId());
                }
                $tag->save();
                $currentParentTag = $tag;
            }
        }
        return $currentParentTag;
    }
}

<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Factory;

use Pimcore\Bundle\DataImporterBundle\Exception\InvalidConfigurationException;
use Pimcore\Bundle\DataImporterBundle\Mapping\Operator\AbstractOperator;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\Data\Video;
use Torq\PimcoreHelpersBundle\Service\Utility\ArrayUtils;

class AsVideo extends AbstractOperator
{
    protected string $videoType;

    public function __construct(ApplicationLogger $applicationLogger, private ArrayUtils $utils)
    {
        parent::__construct($applicationLogger);
    }

    public function setSettings(array $settings): void
    {
        $type = $this->utils->get('videoType', $settings);
        if ($type === null) {
            throw new InvalidConfigurationException("Please select a video type.");
        }
        $this->videoType = $type;
    }


    public function process($inputData, bool $dryRun = false)
    {
        if (is_array($inputData)) {
            $output = [];
            foreach ($inputData as $datum) {
                $output[] = $this->createVideo($datum);
            }
        } else {
            $output = $this->createVideo($inputData);
        }
        return $output;
    }

    public function evaluateReturnType(string $inputType, ?int $index = null): string
    {
        return str_contains(mb_strtolower($inputType), 'array') ? 'array' : 'video';
    }

    private function createVideo(string $url)
    {
        $video = new Video();
        $video->setType($this->videoType);
        $video->setData($url);
        return $video;
    }
}
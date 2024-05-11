<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

class RegexReplace extends \Pimcore\Bundle\DataImporterBundle\Mapping\Operator\Simple\StringReplace
{

    /**
     * @param mixed $inputData
     * @param bool $dryRun
     *
     * @return array|false|mixed|null
     */
    public function process($inputData, bool $dryRun = false)
    {
        $returnScalar = false;
        if (!is_array($inputData)) {
            $returnScalar = true;
            $inputData = [$inputData];
        }

        foreach ($inputData as &$data) {
            $data = preg_replace($this->search, $this->replace, $data);
        }

        if ($returnScalar) {
            if (!empty($inputData)) {
                return reset($inputData);
            }

            return null;
        } else {
            return $inputData;
        }
    }

}
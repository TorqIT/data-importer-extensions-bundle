<?php

namespace TorqIT\DataImporterExtensionsBundle\Mapping\Operator\Simple;

use Symfony\Component\Intl\Countries;

class AsCountryCode extends \Pimcore\Bundle\DataImporterBundle\Mapping\Operator\Simple\StringReplace
{

    /**
     * @param mixed $inputData
     * @param bool $dryRun
     *
     * @return array|false|mixed|null
     */
    public function process($inputData, bool $dryRun = false)
    {
        $validCountryCode = null;
        $countryCode = strtoupper($inputData);

        if (strlen($countryCode) === 2) {
            if (Countries::exists($countryCode)) {
                $validCountryCode = $countryCode;
            }
        } elseif (strlen($countryCode) === 3) {
            if (Countries::alpha3CodeExists($countryCode)) {
                $validCountryCode = Countries::getAlpha2Code($countryCode);
            }
        }

        if ($validCountryCode === null) {
            throw new \Exception('Invalid country code: ' . $countryCode);
        }

        return $validCountryCode;
    }

}

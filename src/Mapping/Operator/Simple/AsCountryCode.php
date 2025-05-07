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
     *
     * Given input of 2- or 3- character country code, returns the 2-character country code.
     * If the input is not a valid country code, returns an empty string.
     */
    public function process($inputData, bool $dryRun = false)
    {
        $validCountryCode = '';
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

        return $validCountryCode;
    }

}

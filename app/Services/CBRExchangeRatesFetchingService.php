<?php

namespace App\Services;

use App\ExchangeRates;
use DOMDocument;
use DOMElement;

final class CBRExchangeRatesFetchingService implements ExchangeRatesServiceContract
{
    private const VALUTE_IDS = ['R01235'];

    public function update(): bool
    {
        $xml = new DOMDocument();
        $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date('d.m.Y');

        if (! $xml->load($url)) {
            return false;
        }

        foreach ($xml->documentElement->getElementsByTagName('Valute') as $item) {
            /* @var DOMElement $item */
            if (in_array($item->attributes->getNamedItem('ID')->nodeValue, self::VALUTE_IDS, true)) {
                $isSuccess = $this->addData($item);

                if (! $isSuccess) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param DOMElement $item
     *
     * @return bool
     */
    private function addData(DOMElement $item): bool
    {
        $model = new ExchangeRates([
            'num_code' => $item->getElementsByTagName('NumCode')->item(0)->nodeValue,
            'char_code' => $item->getElementsByTagName('CharCode')->item(0)->nodeValue,
            'nominal' => $item->getElementsByTagName('Nominal')->item(0)->nodeValue,
            'name' => $item->getElementsByTagName('Name')->item(0)->nodeValue,
            'value' => (float) str_replace(
                ',',
                '.',
                $item->getElementsByTagName('Value')->item(0)->nodeValue
            ),
        ]);

        return $model->save();
    }
}

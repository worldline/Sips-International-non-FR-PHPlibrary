<?php

namespace Sips;

use \InvalidArgumentException;

class SipsCurrency {

    public static $currencies = array(
        'EUR' => '978', 'USD' => '840', 'CHF' => '756', 'GBP' => '826',
        'CAD' => '124', 'JPY' => '392', 'MXP' => '484', 'TRY' => '949',
        'AUD' => '036', 'NZD' => '554', 'NOK' => '578', 'BRC' => '986',
        'ARP' => '032', 'KHR' => '116', 'TWD' => '901', 'SEK' => '752',
        'DKK' => '208', 'KRW' => '410', 'SGD' => '702', 'XPF' => '953',
        'XOF' => '952'
    );

    static function convertCurrencyToSipsCurrencyCode($currency)
    {
        if(!in_array($currency, array_keys(self::$currencies)))
            throw new InvalidArgumentException("Unknown currencyCode $currency.");
        return self::$currencies[$currency];
    }

    static function convertSipsCurrencyCodeToCurrency($code)
    {
        if(!in_array($code, array_values(self::$currencies)))
            throw new InvalidArgumentException("Unknown sipsCode $code.");
        return array_search($code, self::$currencies);
    }
}
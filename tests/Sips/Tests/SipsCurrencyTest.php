<?php

namespace Sips\Tests;

use Sips\SipsCurrency;

class SipsCurrencyTest extends \TestCase
{
    /** @test */
    public function HasCurrencyList()
    {
        $sipsCurrency = new SipsCurrency();
        $this->assertEquals(
            array(
                'EUR' => '978', 'USD' => '840', 'CHF' => '756', 'GBP' => '826',
                'CAD' => '124', 'JPY' => '392', 'MXP' => '484', 'TRY' => '949',
                'AUD' => '036', 'NZD' => '554', 'NOK' => '578', 'BRC' => '986',
                'ARP' => '032', 'KHR' => '116', 'TWD' => '901', 'SEK' => '752',
                'DKK' => '208', 'KRW' => '410', 'SGD' => '702', 'XPF' => '953',
                'XOF' => '952'
            ),
            $sipsCurrency->getCurrencies()
        );
    }

    /**
     * @test
     */
    public function CanConvertSipsCurrencyCodeToCurrency()
    {
        $this->assertEquals("EUR",SipsCurrency::convertSipsCurrencyCodeToCurrency(978));
    }

    /**
     * @test
     */
    public function CanConvertCurrencyToSipsCurrencyCode()
    {
        $this->assertEquals("978",SipsCurrency::convertCurrencyToSipsCurrencyCode('EUR'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function InvalidSipsCurrencyCodeThrowsException()
    {
        $currency = SipsCurrency::convertSipsCurrencyCodeToCurrency(1234);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function InvalidCurrencyThrowsException()
    {
        $sipsCurrencyCode = SipsCurrency::convertCurrencyToSipsCurrencyCode('UNKNOWN_CURRENCY');
    }
}
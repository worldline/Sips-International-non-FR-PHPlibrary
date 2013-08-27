<?php

namespace Sips\Tests\ShaComposer;

use Sips\Passphrase;
use Sips\ShaComposer\AllParametersShaComposer;

class AllParametersShaComposerTest extends \TestCase
{
    /**
     * @test
     * @dataProvider provideRequest          
     */
    public function ShaStringIsComposedCorrectly(Passphrase $passphrase, array $request, $expectedSha)
    {
        $composer = new AllParametersShaComposer($passphrase);
        $this->assertEquals($expectedSha, $composer->compose($request));
    }
    
    public function provideRequest()
    {
        $passphrase = new Passphrase('002001000000001_KEY1');
        
        $expectedSha1 = '65421e720d149e8a79bdc9a29f6c462ed199b78f32e558c6c5fc15f0084e5384';
        $request1 = array(
            'amount' => 1234,
            'currencyCode' => 978,
            'merchantId' => '002001000000001',
            'normalReturnUrl' => 'http://www.normalreturnurl.com',
            'transactionReference' => 'marlon1',
            'keyVersion' => 1
        );
        
        $expectedSha2 = '6f3a60affb6002355cf214f7e630fa5faba993ddcb47c2d1d942773e4c63f896';
        $request2 = array(
            'amount' => 9876,
            'currencyCode' => 978,
            'merchantId' => '002001000000001',
            'normalReturnUrl' => 'http://www.normalreturnurl.com',
            'transactionReference' => 'marlon2',
            'keyVersion' => 1,
            'customerLanguage' => 'nl'
        );
        
        return array(
            array($passphrase, $request1, $expectedSha1),
            array($passphrase, $request2, $expectedSha2),
        );
    }
}
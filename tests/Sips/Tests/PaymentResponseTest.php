<?php

namespace Sips\Tests;

use Sips\Tests\ShaComposer\FakeShaComposer;
use Sips\ShaComposer\ShaComposer;
use Sips\PaymentResponse;

class PaymentResponseTest extends \TestCase
{    
    /** @test */ 
    /*
    public function CanBeVerified()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertTrue($paymentResponse->isValid(new FakeShaComposer));
    }*/
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    /*
    public function CannotExistWithoutShaSign()
    {
        $paymentResponse = new PaymentResponse(array());
    }
     * 
     */
    
    /** @test */ /*
    public function ParametersCanBeRetrieved()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertEquals($aRequest['orderID'], $paymentResponse->getParam('orderid'));
    }*/
    
    /** @test */ /*
    public function ChecksStatus()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertTrue($paymentResponse->isSuccessful());
    }*/
    
    /** @test */ /*
    public function AmountIsConvertedToCent()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertEquals(100, $paymentResponse->getParam('amount'));
    }*/
    /*
    public function provideFloats()
    {
        return array(
            array('17.89', 1779),
            array('65.35', 6535),
            array('12.99', 1299),
        );
    }
    
    public function CorrectlyConvertsFloatAmountsToInteger($string, $integer)
    {
        $paymentResponse = new PaymentResponse(array('amount' => $string, 'shasign' => '123'));
        $this->assertEquals($integer, $paymentResponse->getParam('amount'));
    }
    
    private function provideRequest()
    {
        return array(
            'orderID' => '123',
            'amount' => 1,
            ''
        )
    }
    */
    
}
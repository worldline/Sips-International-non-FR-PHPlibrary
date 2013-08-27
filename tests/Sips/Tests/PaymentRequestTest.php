<?php

namespace Sips\Tests;

use Sips\Tests\ShaComposer\FakeShaComposer;
use Sips\PaymentRequest;

class PaymentRequestTest extends \TestCase
{
    /** @test */
    public function IsValidWhenRequiredFieldsAreFilledIn()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->validate();
    }
    
    /** @test */
    public function IsValidWhenAllFieldsAreFilledIn()
    {
        $paymentRequest = $this->provideCompletePaymentRequest();
        $paymentRequest->validate();
    }
    
    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function IsInvalidWhenFieldsAreMissing()
    {
        $paymentRequest = new PaymentRequest(new FakeShaComposer);
        $paymentRequest->validate();
    }
    
    /** @test */
    public function UnimportantParamsUseMagicSetters()
    {
        $paymentRequest = new PaymentRequest(new FakeShaComposer);
        $paymentRequest->setTemplateName('Marlon Sips Test');
        $this->assertEquals('Marlon Sips Test', $paymentRequest->getTemplateName());
    }
    
    /**
     * @test
     * @dataProvider provideBadParameters
     * @expectedException \InvalidArgumentException
     */
    public function BadParametersCauseExceptions($method, $value)
    {
        $paymentRequest = new PaymentRequest(new FakeShaComposer);
        $paymentRequest->$method($value);
    }
    
    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function UnknownMethodFails()
    {
        $paymentRequest = new PaymentRequest(new FakeShaComposer);
        $paymentRequest->getFoobar();
    }
    
    public function provideBadParameters()
    {
        $notAUri = 'http://not a uri';
        
        return array(
            array('setAmount', 10.50),
            array('setAmount', -1),
            array('setCurrency', 'Belgische Frank'),
            array('setNormalReturnUrl', $notAUri),
            array('setLanguage', 'West-Vlaams')
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function CreateFromArrayInvalid()
    {
        $paymentRequest = PaymentRequest::createFromArray(new FakeShaComposer, array('language'=>'West-Vlaams'));
    }            
}
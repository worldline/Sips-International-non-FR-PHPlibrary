<?php

use Sips\Tests\ShaComposer\FakeShaComposer;
use Sips\PaymentRequest;

require_once 'PHPUnit/Framework/TestCase.php';
require_once __DIR__.'/Sips/Tests/ShaComposer/FakeShaComposer.php';

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /** @return PaymentRequest */
    protected function provideMinimalPaymentRequest()
    {
        $paymentRequest = PaymentRequest::createFromArray(new FakeShaComposer(), array(
            'merchantId' => '002001000000001',
            'normalReturnUrl' => 'http://www.normalreturnurl.com',
            'transactionReference' => '123456',
            'keyVersion' => 1
        ));
        
        $paymentRequest->setSipsUri(PaymentRequest::TEST);
        
        // minimal required fields for Sips (together with merchantId, normalReturnUrl, transactionReference, keyVersion)
        $paymentRequest->setAmount(100);
        $paymentRequest->setCurrency("EUR");
        
        return $paymentRequest;        
    }
    
    /** @return PaymentRequest */
    protected function provideCompletePaymentRequest()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        
        $paymentRequest->setLanguage('nl');
        $paymentRequest->setTemplateName('Marlon Sips Test');
        
        return $paymentRequest;
    }
}
<?php

namespace Sips\Tests;

use Sips\Tests\ShaComposer\FakeShaComposer;
use Sips\ShaComposer\ShaComposer;
use Sips\PaymentResponse;

class PaymentResponseTest extends \TestCase
{
    /**
     * @test
     */
    public function CanBeVerified()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $shaComposer = $this->createMock('Sips\ShaComposer\ShaComposer');

        $shaComposer->expects($this->once())
                    ->method('compose')
                    ->with(
                        $this->equalTo(
                            array (
                                'captureDay' => '0',
                                'captureMode' => 'IMMEDIATE',
                                'currencyCode' => '978',
                                'merchantId' => '225005000270001',
                                'orderChannel' => 'INTERNET',
                                'responseCode' => '00',
                                'transactionDateTime' => '2013-11-13T15:30:27+01:00',
                                'transactionReference' => 'marlon8452838c8ae08aa',
                                'keyVersion' => '1',
                                'acquirerResponseCode' => '00',
                                'amount' => '3',
                                'authorisationId' => '009852',
                                'guaranteeIndicator' => 'Y',
                                'cardCSCResultCode' => '',
                                'panExpiryDate' => '201709',
                                'paymentMeanBrand' => 'BCMC',
                                'paymentMeanType' => 'CARD',
                                'complementaryCode' => '',
                                'complementaryInfo' => '',
                                'customerIpAddress' => '193.58.82.178',
                                'maskedPan' => '6703###########12',
                                'merchantTransactionDateTime' => '2013-11-13T15:28:27+01:00',
                                'scoreValue' => '0.0',
                                'scoreColor' => 'GREEN',
                                'scoreInfo' => 'A3;N#SC;N;TRANS=2:2;CUMUL=5:250000',
                                'scoreProfile' => '25',
                                'scoreThreshold' => '-10.0',
                                'holderAuthentRelegation' => 'N',
                                'holderAuthentStatus' => '3D_SUCCESS',
                                'transactionOrigin' => 'INTERNET',
                                'paymentPattern' => 'ONE_SHOT',
                                'customerMobilePhone' => 'null',
                                'mandateAuthentMethod' => 'null',
                                'mandateUsage' => 'null',
                                'transactionActors' => 'null',
                                'mandateId' => 'null',
                                'captureLimitDate' => 'null',
                                'dccStatus' => 'null',
                                'dccResponseCode' => 'null',
                                'dccAmount' => 'null',
                                'dccCurrencyCode' => 'null',
                                'dccExchangeRate' => 'null',
                                'dccExchangeRateValidity' => 'null',
                                'dccProvider' => 'null',
                                'statementReference' => 'null',
                                'panEntryMode' => 'null',
                                'walletType' => 'null',
                                'holderAuthentMethod' => 'null',
                            )
                        )
                    )
                    ->will($this->returnValue('30113e7fe4cbd109f579a3c12ad71581b165f4cb340a3137289dff9e9609daa0'));

        $this->assertTrue($paymentResponse->isValid($shaComposer));
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function CannotExistWithoutShaSign()
    {
        $paymentResponse = new PaymentResponse(array());
    }

    /**
     * @test
     */
    public function ParametersCanBeRetrieved()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertEquals('009852', $paymentResponse->getParam('authorisationId'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function UnknownParametersThrowException()
    {
        $aRequest = $this->provideRequest();

        $paymentResponse = new PaymentResponse($aRequest);
        $paymentResponse->getParam('thisParamDoesNotExist');
    }

    /**
     * @test
     */
    public function RetrieveDataString()
    {
        $aRequest = $this->provideRequest();

        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertEquals("captureDay=0|captureMode=IMMEDIATE|currencyCode=978|merchantId=225005000270001|orderChannel=INTERNET|responseCode=00|transactionDateTime=2013-11-13T15:30:27+01:00|transactionReference=marlon8452838c8ae08aa|keyVersion=1|acquirerResponseCode=00|amount=3|authorisationId=009852|guaranteeIndicator=Y|cardCSCResultCode=|panExpiryDate=201709|paymentMeanBrand=BCMC|paymentMeanType=CARD|complementaryCode=|complementaryInfo=|customerIpAddress=193.58.82.178|maskedPan=6703###########12|merchantTransactionDateTime=2013-11-13T15:28:27+01:00|scoreValue=0.0|scoreColor=GREEN|scoreInfo=A3;N#SC;N;TRANS=2:2;CUMUL=5:250000|scoreProfile=25|scoreThreshold=-10.0|holderAuthentRelegation=N|holderAuthentStatus=3D_SUCCESS|transactionOrigin=INTERNET|paymentPattern=ONE_SHOT|customerMobilePhone=null|mandateAuthentMethod=null|mandateUsage=null|transactionActors=null|mandateId=null|captureLimitDate=null|dccStatus=null|dccResponseCode=null|dccAmount=null|dccCurrencyCode=null|dccExchangeRate=null|dccExchangeRateValidity=null|dccProvider=null|statementReference=null|panEntryMode=null|walletType=null|holderAuthentMethod=null", $paymentResponse->getDataString());
    }

    /**
     * @test
     */
    public function CanGenerateArrayFormat()
    {
        $aRequest = $this->provideRequest();

        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertEquals(
            array (
                'captureDay' => '0',
                'captureMode' => 'IMMEDIATE',
                'currencyCode' => '978',
                'merchantId' => '225005000270001',
                'orderChannel' => 'INTERNET',
                'responseCode' => '00',
                'transactionDateTime' => '2013-11-13T15:30:27+01:00',
                'transactionReference' => 'marlon8452838c8ae08aa',
                'keyVersion' => '1',
                'acquirerResponseCode' => '00',
                'amount' => '3',
                'authorisationId' => '009852',
                'guaranteeIndicator' => 'Y',
                'cardCSCResultCode' => '',
                'panExpiryDate' => '201709',
                'paymentMeanBrand' => 'BCMC',
                'paymentMeanType' => 'CARD',
                'complementaryCode' => '',
                'complementaryInfo' => '',
                'customerIpAddress' => '193.58.82.178',
                'maskedPan' => '6703###########12',
                'merchantTransactionDateTime' => '2013-11-13T15:28:27+01:00',
                'scoreValue' => '0.0',
                'scoreColor' => 'GREEN',
                'scoreInfo' => 'A3;N#SC;N;TRANS=2:2;CUMUL=5:250000',
                'scoreProfile' => '25',
                'scoreThreshold' => '-10.0',
                'holderAuthentRelegation' => 'N',
                'holderAuthentStatus' => '3D_SUCCESS',
                'transactionOrigin' => 'INTERNET',
                'paymentPattern' => 'ONE_SHOT',
                'customerMobilePhone' => 'null',
                'mandateAuthentMethod' => 'null',
                'mandateUsage' => 'null',
                'transactionActors' => 'null',
                'mandateId' => 'null',
                'captureLimitDate' => 'null',
                'dccStatus' => 'null',
                'dccResponseCode' => 'null',
                'dccAmount' => 'null',
                'dccCurrencyCode' => 'null',
                'dccExchangeRate' => 'null',
                'dccExchangeRateValidity' => 'null',
                'dccProvider' => 'null',
                'statementReference' => 'null',
                'panEntryMode' => 'null',
                'walletType' => 'null',
                'holderAuthentMethod' => 'null'
            ),
            $paymentResponse->toArray()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function NoDataParameterThrowsException()
    {
        $aRequest = array(
            'Seal' => "30113e7fe4cbd109f579a3c12ad71581b165f4cb340a3137289dff9e9609daa0",
            "InterfaceVersion" => "HP_2.4"
        );
        $paymentResponse = new PaymentResponse($aRequest);
    }

    /**
     * @test
     */
    public function ChecksStatus()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertTrue($paymentResponse->isSuccessful());
    }

    /**
     * @test
     */
    public function AmountIsConvertedToCent()
    {
        $aRequest = $this->provideRequest();
        
        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertEquals(3, $paymentResponse->getParam('amount'));
    }

    /**
     * @test
     */
    public function RetrieveSeal()
    {
        $aRequest = $this->provideRequest();

        $paymentResponse = new PaymentResponse($aRequest);
        $this->assertEquals('30113e7fe4cbd109f579a3c12ad71581b165f4cb340a3137289dff9e9609daa0', $paymentResponse->getSeal());
    }

    private function provideRequest()
    {
        return array(
            'Data' => "captureDay=0|captureMode=IMMEDIATE|currencyCode=978|merchantId=225005000270001|orderChannel=INTERNET|responseCode=00|transactionDateTime=2013-11-13T15:30:27+01:00|transactionReference=marlon8452838c8ae08aa|keyVersion=1|acquirerResponseCode=00|amount=3|authorisationId=009852|guaranteeIndicator=Y|cardCSCResultCode=|panExpiryDate=201709|paymentMeanBrand=BCMC|paymentMeanType=CARD|complementaryCode=|complementaryInfo=|customerIpAddress=193.58.82.178|maskedPan=6703###########12|merchantTransactionDateTime=2013-11-13T15:28:27+01:00|scoreValue=0.0|scoreColor=GREEN|scoreInfo=A3;N#SC;N;TRANS=2:2;CUMUL=5:250000|scoreProfile=25|scoreThreshold=-10.0|holderAuthentRelegation=N|holderAuthentStatus=3D_SUCCESS|transactionOrigin=INTERNET|paymentPattern=ONE_SHOT|customerMobilePhone=null|mandateAuthentMethod=null|mandateUsage=null|transactionActors=null|mandateId=null|captureLimitDate=null|dccStatus=null|dccResponseCode=null|dccAmount=null|dccCurrencyCode=null|dccExchangeRate=null|dccExchangeRateValidity=null|dccProvider=null|statementReference=null|panEntryMode=null|walletType=null|holderAuthentMethod=null",
            'Seal' => "30113e7fe4cbd109f579a3c12ad71581b165f4cb340a3137289dff9e9609daa0",
            "InterfaceVersion" => "HP_2.4"
        );
    }
}

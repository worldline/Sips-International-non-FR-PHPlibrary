<?php

namespace Sips\Tests;

use Sips\ShaComposer\AllParametersShaComposer;
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
     */
    public function BillingInfoCanBeSet()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingContactFirstname("Mathieu");
        $paymentRequest->setBillingContactLastname("Duffeler");
        $paymentRequest->setBillingAddressStreet("Wandellaan");
        $paymentRequest->setBillingAddressStreetNumber("56");
        $paymentRequest->setBillingAddressCity("Wielsbeke");
        $paymentRequest->setBillingAddressZipCode("8710");
        $paymentRequest->setBillingContactEmail("mathieu@duffeler.be");
        $paymentRequest->setBillingContactPhone("+32472442151");


        $arrayRepresentation = $paymentRequest->toArray();

        $this->assertEquals('Mathieu', $arrayRepresentation['billingContact.firstname']);
        $this->assertEquals('Duffeler', $arrayRepresentation['billingContact.lastname']);
        $this->assertEquals('Wandellaan', $arrayRepresentation['billingAddress.street']);
        $this->assertEquals('56', $arrayRepresentation['billingAddress.streetNumber']);
        $this->assertEquals('Wielsbeke', $arrayRepresentation['billingAddress.city']);
        $this->assertEquals('8710', $arrayRepresentation['billingAddress.zipCode']);
        $this->assertEquals('mathieu@duffeler.be', $arrayRepresentation['billingContact.email']);
        $this->assertEquals('+32472442151', $arrayRepresentation['billingContact.phone']);
    }

    /**
     * @test
     */
    public function BillingInfoIsNormalized()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingContactFirstname("Mâthìéû");
        $paymentRequest->setBillingContactLastname("Dùffélèr");
        $paymentRequest->setBillingAddressStreet("Wàndéllâän");
        $paymentRequest->setBillingAddressStreetNumber("56");
        $paymentRequest->setBillingAddressCity("Wìélsbèkê");
        $paymentRequest->setBillingAddressZipCode("8710");
        $paymentRequest->setBillingContactEmail("mathieu@duffeler.be");
        $paymentRequest->setBillingContactPhone("+32472442151");


        $arrayRepresentation = $paymentRequest->toArray();

        $this->assertEquals('Mathieu', $arrayRepresentation['billingContact.firstname']);
        $this->assertEquals('Duffeler', $arrayRepresentation['billingContact.lastname']);
        $this->assertEquals('Wandellaan', $arrayRepresentation['billingAddress.street']);
        $this->assertEquals('56', $arrayRepresentation['billingAddress.streetNumber']);
        $this->assertEquals('Wielsbeke', $arrayRepresentation['billingAddress.city']);
        $this->assertEquals('8710', $arrayRepresentation['billingAddress.zipCode']);
        $this->assertEquals('mathieu@duffeler.be', $arrayRepresentation['billingContact.email']);
        $this->assertEquals('+32472442151', $arrayRepresentation['billingContact.phone']);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function InvalidBillingContactEmailThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingContactEmail("invalidemail");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function ToLongBillingContactEmailThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingContactEmail("thisemailaddresscontainsmorethenfiftycharacters@andwillthrowanexception.be");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function ToLongBillingAddressStreetThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingAddressStreet("thisBillingAddressStreetContainsMoreThen35CharactersAndWillThrowAnException");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function ToLongBillingAddressStreetNumberThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingAddressStreetNumber("12345678910");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function ToLongBillingAddressZipCodeThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingAddressZipCode("12345678910");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function ToLongBillingAddressCityThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingAddressCity("thisBillingAddressCityContainsMoreThen25CharactersAndWillThrowAnException");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function ToLongBillingContactPhoneThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setBillingContactPhone("0123456468434684646843486464844984646846848946546848648946548949");
    }

    /**
     * @test
     */
    public function GetShaSign()
    {
        $shaComposer = $this->getMock('Sips\ShaComposer\ShaComposer');
        $paymentRequest = PaymentRequest::createFromArray($shaComposer, array(
            'merchantId' => '002001000000001',
            'normalReturnUrl' => 'http://www.normalreturnurl.com',
            'transactionReference' => '123456',
            'keyVersion' => 1
        ));

        $paymentRequest->setSipsUri(PaymentRequest::TEST);

        // minimal required fields for Sips (together with merchantId, normalReturnUrl, transactionReference, keyVersion)
        $paymentRequest->setAmount(100);
        $paymentRequest->setCurrency("EUR");



        $shaComposer->expects($this->once())
                    ->method('compose')
                    ->with(
                        $this->equalTo(
                            array(
                                'merchantId' => "002001000000001",
                                'normalReturnUrl' => "http://www.normalreturnurl.com",
                                'transactionReference' => "123456",
                                'keyVersion' => 1,
                                'amount' => 100,
                                'currencyCode' => "978"
                            )
                        )
                    )
                    ->will($this->returnValue('seal'));

        $this->assertEquals('seal', $paymentRequest->getShaSign());
    }

    /**
     * @test
     */
    public function CanGetSipsUri()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $this->assertEquals(PaymentRequest::TEST, $paymentRequest->getSipsUri());
    }

    /**
     * @test
     */
    public function CanSetAutomaticResponseUrl()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setAutomaticResponseUrl("http://sips.webshop.marlon.be");
        $this->assertEquals("http://sips.webshop.marlon.be", $paymentRequest->getautomaticResponseUrl());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function TransactionReferenceCannotContainSpecialChars()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setTransactionReference("%%!^:");
    }

    /**
     * @test
     */
    public function CanSetPaymentMeanBrand()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setPaymentBrand("BCMC");
        $this->assertEquals("BCMC", $paymentRequest->getPaymentMeanBrandList());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function UnknownBrandThrowsError()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setPaymentBrand("This brand does not exists");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function LongUriThrowsException()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $paymentRequest->setNormalReturnUrl("http://lqksdjfslkdfjsdlkfjsdlkfjsdlkfjsdlkfjsdmlfqkjdslkgjsdklgjsqdlkgjskldgjslqmdgkjqsdmlgkjsqdmglksdjgmlsqdkgjsmdqlgkjqmlskjdgmqlskjgqsdmlkgjqsmldgkjqlmskdgjqsdmlgkjqdsmlgjqsgomisqdjgmoqsgijqskldjqmsodgijqsmogi.be");
    }

    /**
     * @test
     */
    public function CanGenerateParameterString()
    {
        $paymentRequest = $this->provideMinimalPaymentRequest();
        $this->assertEquals("merchantId=002001000000001|normalReturnUrl=http://www.normalreturnurl.com|transactionReference=123456|keyVersion=1|amount=100|currencyCode=978", $paymentRequest->toParameterString());
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

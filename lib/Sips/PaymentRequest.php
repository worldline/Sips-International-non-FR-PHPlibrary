<?php

namespace Sips;

use Sips\ShaComposer\ShaComposer;
use \BadMethodCallException;
use \InvalidArgumentException;
use Sips\Normalizer;

class PaymentRequest
{
    const SIMU = "https://payment-webinit.simu.sips-atos.com/paymentInit";
    const TEST = "https://payment-webinit.test.sips-atos.com/paymentInit";
    const PRODUCTION = "https://payment-webinit.sips-atos.com/paymentInit";

    private $brandsmap = array(
        'ACCEPTGIRO' => 'CREDIT_TRANSFER',
        'AMEX' => 'CARD',
        'BCMC' => 'CARD',
        'BUYSTER' => 'CARD',
        'BANK CARD' => 'CARD',
        'IDEAL' => 'CREDIT_TRANSFER',
        'INCASSO' => 'DIRECT_DEBIT',
        'MAESTRO' => 'CARD',
        'MASTERCARD' => 'CARD',
        'MINITIX' => 'OTHER',
        'NETBANKING' => 'CREDIT_TRANSFER',
        'PAYPAL' => 'CARD',
        'REFUND' => 'OTHER',
        'SDD' => 'DIRECT_DEBIT',
        'SOFORT' => 'CREDIT_TRANSFER',
        'VISA' => 'CARD',
        'VPAY' => 'CARD',
        'VISA ELECTRON' => 'CARD',
        'CBCONLINE' => 'CREDIT_TRANSFER',
        'KBCONLINE' => 'CREDIT_TRANSFER'
    );

    /** @var ShaComposer */
    private $shaComposer;

    private $sipsUri = self::TEST;

    private $parameters = array();

    private $sipsFields = array(
        'amount', 'currencyCode', 'merchantId', 'normalReturnUrl',
        'transactionReference', 'keyVersion', 'paymentMeanBrand', 'customerLanguage',
        'billingAddress.city', 'billingAddress.company', 'billingAddress.country',
        'billingAddress', 'billingAddress.postBox', 'billingAddress.state',
        'billingAddress.street', 'billingAddress.streetNumber', 'billingAddress.zipCode',
        'billingContact.email', 'billingContact.firstname', 'billingContact.gender',
        'billingContact.lastname', 'billingContact.mobile', 'billingContact.phone',
        'customerAddress', 'customerAddress.city', 'customerAddress.company',
        'customerAddress.country', 'customerAddress.postBox', 'customerAddress.state',
        'customerAddress.street', 'customerAddress.streetNumber', 'customerAddress.zipCode',
        'customerContact', 'customerContact.email', 'customerContact.firstname',
        'customerContact.gender', 'customerContact.lastname', 'customerContact.mobile',
        'customerContact.phone', 'customerContact.title', 'expirationDate', 'automaticResponseUrl',
        'templateName','paymentMeanBrandList'
    );

    private $requiredFields = array(
        'amount', 'currencyCode', 'merchantId', 'normalReturnUrl',
        'transactionReference', 'keyVersion'
    );

    public $allowedlanguages = array(
        'nl', 'fr', 'de', 'it', 'es', 'cy', 'en'
    );

    public function __construct(ShaComposer $shaComposer)
    {
        $this->shaComposer = $shaComposer;
    }

    /** @return string */
    public function getShaSign()
    {
        return $this->shaComposer->compose($this->toArray());
    }

    /** @return string */
    public function getSipsUri()
    {
        return $this->sipsUri;
    }

    public function setSipsUri($sipsUri)
    {
        $this->validateUri($sipsUri);
        $this->sipsUri = $sipsUri;
    }

    public function setNormalReturnUrl($url)
    {
        $this->validateUri($url);
        $this->parameters['normalReturnUrl'] = $url;
    }

    public function setAutomaticResponseUrl($url)
    {
        $this->validateUri($url);
        $this->parameters['automaticResponseUrl'] = $url;
    }

    public function setTransactionReference($transactionReference)
    {
        if(preg_match('/[^a-zA-Z0-9_-]/', $transactionReference)) {
            throw new \InvalidArgumentException("TransactionReference cannot contain special characters");
        }
        $this->parameters['transactionReference'] = $transactionReference;
    }

    /**
	 * Set amount in cents, eg EUR 12.34 is written as 1234
	 */
	public function setAmount($amount)
	{
		if(!is_int($amount)) {
			throw new InvalidArgumentException("Integer expected. Amount is always in cents");
		}
		if($amount <= 0) {
			throw new InvalidArgumentException("Amount must be a positive number");
		}
		$this->parameters['amount'] = $amount;

	}

    public function setCurrency($currency)
	{
		if(!array_key_exists(strtoupper($currency), SipsCurrency::getCurrencies())) {
			throw new InvalidArgumentException("Unknown currency");
		}
		$this->parameters['currencyCode'] = SipsCurrency::convertCurrencyToSipsCurrencyCode($currency);
	}

	public function setLanguage($language)
	{
		if(!in_array($language, $this->allowedlanguages)) {
			throw new InvalidArgumentException("Invalid language locale");
		}
		$this->parameters['customerLanguage'] = $language;
	}

    public function setPaymentBrand($brand)
    {
        $this->parameters['paymentMeanBrandList'] = '';
        if(!array_key_exists(strtoupper($brand), $this->brandsmap)) {
            throw new InvalidArgumentException("Unknown Brand [$brand].");
        }
        $this->parameters['paymentMeanBrandList'] = strtoupper($brand);
    }

    public function setBillingContactEmail($email)
    {
        if(strlen($email) > 50) {
            throw new InvalidArgumentException("Email is too long");
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email is invalid");
        }
        $this->parameters['billingContact.email'] = $email;
    }

    public function setBillingAddressStreet($street)
    {
        if(strlen($street) > 35) {
            throw new InvalidArgumentException("street is too long");
        }
        $this->parameters['billingAddress.street'] = Normalizer::normalize($street);
    }

    public function setBillingAddressStreetNumber($nr)
    {
        if(strlen($nr) > 10) {
            throw new InvalidArgumentException("streetNumber is too long");
        }
        $this->parameters['billingAddress.streetNumber'] = Normalizer::normalize($nr);
    }

    public function setBillingAddressZipCode($zipCode)
    {
        if(strlen($zipCode) > 10) {
            throw new InvalidArgumentException("zipCode is too long");
        }
        $this->parameters['billingAddress.zipCode'] = Normalizer::normalize($zipCode);
    }

    public function setBillingAddressCity($city)
    {
        if(strlen($city) > 25) {
            throw new InvalidArgumentException("city is too long");
        }
        $this->parameters['billingAddress.city'] = Normalizer::normalize($city);
    }

    public function setBillingContactPhone($phone)
    {
        if(strlen($phone) > 30) {
            throw new InvalidArgumentException("phone is too long");
        }
        $this->parameters['billingContact.phone'] = $phone;
    }

    public function setBillingContactFirstname($firstname)
    {
        $this->parameters['billingContact.firstname'] = str_replace(array("'", '"'), '', Normalizer::normalize($firstname)); // replace quotes
    }

    public function setBillingContactLastname($lastname)
    {
        $this->parameters['billingContact.lastname'] = str_replace(array("'", '"'), '', Normalizer::normalize($lastname)); // replace quotes
    }

    public function __call($method, $args)
    {
        if(substr($method, 0, 3) == 'set') {
            $field = lcfirst(substr($method, 3));
            if(in_array($field, $this->sipsFields)) {
                $this->parameters[$field] = $args[0];
                return;
            }
        }

        if(substr($method, 0, 3) == 'get') {
            $field = lcfirst(substr($method, 3));
            if(array_key_exists($field, $this->parameters)) {
                return $this->parameters[$field];
            }
        }

        throw new BadMethodCallException("Unknown method $method");
    }

    public function toArray()
    {
        $this->validate();
        return $this->parameters;
    }

    public function toParameterString()
    {
        $parameterString = "";
        foreach($this->parameters as $key => $value) {
            $parameterString .= $key . '=' . $value;
            $parameterString .= (array_search($key, array_keys($this->parameters)) != (count($this->parameters)-1)) ? '|' : '';
        }

        return $parameterString;
    }

    /** @return PaymentRequest */
    public static function createFromArray(ShaComposer $shaComposer, array $parameters)
    {
        $instance = new static($shaComposer);
        foreach($parameters as $key => $value)
        {
            $instance->{"set$key"}($value);
        }
        return $instance;
    }

    public function validate()
    {
        foreach($this->requiredFields as $field) {
            if(empty($this->parameters[$field])) {
                throw new \RuntimeException($field . " can not be empty");
            }
        }
    }

    protected function validateUri($uri)
    {
        if(!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Uri is not valid");
        }
        if(strlen($uri) > 200) {
            throw new InvalidArgumentException("Uri is too long");
        }
    }
}

<?php

namespace Sips;

use Sips\ShaComposer\ShaComposer;
use \BadMethodCallException;
use \InvalidArgumentException;

class PaymentRequest
{
    const TEST = "https://payment-webinit.simu.sips-atos.com/paymentInit";
    
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
        'customerContact.phone', 'customerContact.title', 'expirationDate', 'automaticResponseUrl'
    );
    
    private $requiredFields = array(
        'amount', 'currencyCode', 'merchantId', 'normalReturnUrl', 
        'transactionReference', 'keyVersion'
    );
    
    public $allowedcurrencies = array(
        'EUR' => '978', 'USD' => '840', 'CHF' => '756', 'GBP' => '826', 
        'CAD' => '124', 'JPY' => '392', 'MXP' => '484', 'TRY' => '949',
        'AUD' => '036', 'NZD' => '554', 'NOK' => '578', 'BRC' => '986',
        'ARP' => '032', 'KHR' => '116', 'TWD' => '901', 'SEK' => '752',
        'DKK' => '208', 'KRW' => '410', 'SGD' => '702', 'XPF' => '953',
        'XOF' => '952'        
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
		if($amount >= 1.0E+15) {
			throw new InvalidArgumentException("Amount is too high");
		}
		$this->parameters['amount'] = $amount;

	}
    
    public function setCurrency($currency)
	{
		if(!array_key_exists(strtoupper($currency), $this->allowedcurrencies)) {
			throw new InvalidArgumentException("Unknown currency");
		}
		$this->parameters['currencyCode'] = $this->allowedcurrencies[$currency];
	}
        	
	public function setLanguage($language)
	{
		if(!in_array($language, $this->allowedlanguages)) {
			throw new InvalidArgumentException("Invalid language locale");
		}
		$this->parameters['customerLanguage'] = $language;
	}
    
    public function setBrand($brand) {
        $brand = strtoupper($brand);
        if(!array_key_exists($brand, $this->brandsmap)) {
            throw new InvalidArgumentException("Unknown Brand [$brand].");            
        }
        $this->setPaymentMethod($this->brandsmap[$brand]);
        $this->parameters['paymentMeanBrand'] = $brand;
    }
    
    public function setPaymentMethod($paymentMethod)
    {
        $this->setPm($paymentMethod);
    }
    
    public function setPm($pm)
    {
        $pm = strtoupper($pm);
        if(!in_array($pm, $this->brandsmap)) {
            throw new InvalidArgumentException("Unknown Payment method [$pm].");
        }
        $this->parameters['paymentMeanType'] = $pm;        
    }
    
    public function setAllowedPaymentBrands(array $brands)
    {
        $this->parameters['paymentMeanBrandList'] = '';
        foreach($brands as $brand) {
            
            if(!array_key_exists(strtoupper($brand), $this->brandsmap)) {
                throw new InvalidArgumentException("Unknown Brand [$brand].");
            }
            $this->parameters['paymentMeanBrandList'] .= strtoupper($brand);            
            $this->parameters['paymentMeanBrandList'] .= (array_search(strtolower($brand), array_keys($brands)) != (count($brands)-1)) ? ',' : '' ;
        }
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
		$this->parameters['billingAddress.street'] = $street;
    }
    
    public function setBillingAddressStreetNumber($nr)
    {
        if(strlen($nr) > 10) {
			throw new InvalidArgumentException("streetNumber is too long");
		}
        $this->parameters['billingAddress.streetNumber'] = $nr;
    }                        
    
    public function setBillingAddressZipCode($zipCode)
    {
        if(strlen($zipCode) > 10) {
			throw new InvalidArgumentException("zipCode is too long");
		}
		$this->parameters['billingAddress.zipCode'] = $zipCode;
    }
    
    public function setBillingAddressCity($city)
    {
        if(strlen($city) > 25) {
			throw new InvalidArgumentException("city is too long");
		}
		$this->parameters['billingAddress.city'] = $city;
    }
    
    public function setOwnerCountry($ownercountry)
    {
        
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
        $this->parameters['billingContact.firstname'] = str_replace(array("'", '"'), '', $firstname); // replace quotes
    }
    
    public function setBillingContactLastname($lastname)
    {
        $this->parameters['billingContact.lastname'] = str_replace(array("'", '"'), '', $lastname); // replace quotes
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
<?php

namespace Sips;

use Sips\ShaComposer\ShaComposer;
use \BadMethodCallException;
use \InvalidArgumentException;

class PaymentRequest
{
    const TEST = "https://payment-webinit.simu.sips-atos.com/paymentInit";
    
    /** @var ShaComposer */
    private $shaComposer;
    
    private $sipsUri = self::TEST;
    
    private $parameters = array();
    
    private $sipsFields = array(
        'amount', 'currencyCode', 'merchantId', 'normalReturnUrl', 
        'transactionReference', 'keyVersion', 'templateName', 'customerLanguage'
    );
    
    private $requiredFields = array(
        'amount', 'currencyCode', 'merchantId', 'normalReturnUrl', 
        'transactionReference', 'keyVersion'
    );
    
    public $allowedcurrencies = array(
        'EUR' => '978', 'USD' => '840', 'CHF' => '756', 'GBP' => '826', 
        'CAD' => '124', 'JPY' => '392', 'MXP' => '484', 'TRL' => '792',
        'AUD' => '036', 'NZD' => '554', 'NOK' => '578', 'BRC' => '986',
        'ARP' => '032', 'KHR' => '116', 'TWD' => '901', 'SEK' => '752',
        'DKK' => '208', 'KRW' => '410', 'SGD' => '702'
    );
    
    public $allowedlanguages = array(
        'nl', 'fr', 'ge', 'en', 'sp', 'it'
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
    
    public function setNormalReturnUrl($uri)
    {
        $this->validateUri($uri);
        $this->parameters['normalReturnUrl'] = $uri;
    }
    
    public function setOrderid($orderid)
    {
        if(strlen($orderid) > 30) {
            throw new \InvalidArgumentException("Orderid cannot be longer than 30 characters");
        }
        if(preg_match('/[^a-zA-Z0-9_-]/', $orderid)) {
            throw new \InvalidArgumentException("Orderid cannot contain special characters");
        }
        $this->parameters['orderid'] = $orderid;
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
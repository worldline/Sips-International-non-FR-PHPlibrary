<?php

namespace Sips;

use \InvalidArgumentException;
use \SimpleXMLElement;

class PaymentResponse
{   
    /**     
     * @var array
     */
    private $parameters;
    
    public function __construct(array $httpRequest)
    {        
        $xmlResponse = new SimpleXMLElement(base64_decode(strtr($httpRequest['base64Response'], '-_,', '+/=')));
        
        $this->convertXmlResponse($xmlResponse);
    }
    
    private function convertXmlResponse($xmlResponse)
    {
        foreach ($xmlResponse->response[0]->attributes() as $key => $value) {
            $this->parameters[strtoupper($key)] = (string) $value;
        }
    }
    
    /**
	 * Retrieves a response parameter
	 * @param string $param
	 * @throws \InvalidArgumentException
	 */
	public function getParam($key)
	{
		if(method_exists($this, 'get'.$key)) {
			return $this->{'get'.$key}();
		}

		// always use uppercase
		$key = strtoupper($key);

		if(!array_key_exists($key, $this->parameters)) {
			throw new InvalidArgumentException('Parameter ' . $key . ' does not exist.');
		}

		return $this->parameters[$key];
	}
    
    /**
	 * @return int Amount in cents
	 */
	public function getAmount()
	{
		$value = trim($this->parameters['AMOUNT']);

		$withoutDecimals = '#^\d*$#';
		$oneDecimal = '#^\d*\.\d$#';
		$twoDecimals = '#^\d*\.\d\d$#';

		if(preg_match($withoutDecimals, $value)) {
			return (int) ($value.'00');
		}

		if(preg_match($oneDecimal, $value)) {
			return (int) (str_replace('.', '', $value).'0');
		}

		if(preg_match($twoDecimals, $value)) {
			return (int) (str_replace('.', '', $value));
		}

		throw new \InvalidArgumentException("Not a valid currency amount");
	}
    
    public function isSuccessful()
	{		
		return in_array($this->getParam('RESPONSECODE'), array("00", "60"));
	}
    
    public function toArray()
    {
        return $this->parameters;
    }
}
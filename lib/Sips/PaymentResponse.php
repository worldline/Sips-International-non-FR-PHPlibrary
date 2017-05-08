<?php

namespace Sips;

use \InvalidArgumentException;
use Sips\ShaComposer\ShaComposer;

class PaymentResponse
{
    /** @var string */
    const SHASIGN_FIELD = "SEAL";

    /** @var string */
    const DATA_FIELD = "DATA";

    protected $sipsFields = array(
        "captureDay", "captureMode", "currencyCode", "merchantId",
        "orderChannel", "responseCode", "transactionDateTime", "transactionReference",
        "keyVersion", "acquirerResponseCode", "amount", "authorisationId",
        "guaranteeIndicator", "cardCSCResultCode", "panExpiryDate", "paymentMeanBrand",
        "paymentMeanType", "complementaryCode", "complementaryInfo", "customerIpAddress",
        "maskedPan", "merchantTransactionDateTime", "scoreValue", "scoreColor", "scoreInfo",
        "scoreProfile", "scoreThreshold", "holderAuthentRelegation", "holderAuthentStatus",
        "transactionOrigin", "paymentPattern","customerMobilePhone","mandateAuthentMethod",
        "mandateUsage","transactionActors", "mandateId","captureLimitDate","dccStatus",
        "dccResponseCode","dccAmount","dccCurrencyCode","dccExchangeRate", "dccExchangeRateValidity",
        "dccProvider","statementReference","panEntryMode","walletType","holderAuthentMethod"
    );

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $shaSign;

    protected $dataString;

    /**
     * @param array $httpRequest Typically $_REQUEST
     * @throws \InvalidArgumentException
     */
    public function __construct(array $httpRequest)
    {
        // use lowercase internally
        $httpRequest = array_change_key_case($httpRequest, CASE_UPPER);

        // set sha sign
        $this->shaSign = $this->extractShaSign($httpRequest);

        // filter request for Sips parameters
        $this->parameters = $this->filterRequestParameters($httpRequest);
    }

    /**
     * Filter http request parameters
     * @param array $requestParameters
     */
    protected function filterRequestParameters(array $httpRequest)
    {
        //filter request for Sips parameters
        if (!array_key_exists(self::DATA_FIELD, $httpRequest) || $httpRequest[self::DATA_FIELD] == '') {
            throw new InvalidArgumentException('Data parameter not present in parameters.');
        }
        $parameters = array();
        $dataString = $httpRequest[self::DATA_FIELD];
        $this->dataString = $dataString;
        $dataParams = explode('|', $dataString);
        foreach ($dataParams as $dataParamString) {
            $dataKeyValue = explode('=', $dataParamString, 2);
            $parameters[$dataKeyValue[0]] = $dataKeyValue[1];
        }

        return $parameters;
    }

    public function getSeal()
    {
        return $this->shaSign;
    }

    protected function extractShaSign(array $parameters)
    {
        if (!array_key_exists(self::SHASIGN_FIELD, $parameters) || $parameters[self::SHASIGN_FIELD] == '') {
            throw new InvalidArgumentException('SHASIGN parameter not present in parameters.');
        }
        return $parameters[self::SHASIGN_FIELD];
    }

    /**
     * Checks if the response is valid
     * @param ShaComposer $shaComposer
     * @return bool
     */
    public function isValid(ShaComposer $shaComposer)
    {
        return $shaComposer->compose($this->parameters) == $this->shaSign;
    }

    /**
     * Retrieves a response parameter
     * @param string $param
     * @throws \InvalidArgumentException
     */
    public function getParam($key)
    {
        if (method_exists($this, 'get'.$key)) {
            return $this->{'get'.$key}();
        }

        // always use uppercase
        $key = strtoupper($key);
        $parameters = array_change_key_case($this->parameters, CASE_UPPER);
        if (!array_key_exists($key, $parameters)) {
            throw new InvalidArgumentException('Parameter ' . $key . ' does not exist.');
        }

        return $parameters[$key];
    }

    /**
     * @return int Amount in cents
     */
    public function getAmount()
    {
        $value = trim($this->parameters['amount']);
        return (int) ($value);
    }

    public function isSuccessful()
    {
        return in_array($this->getParam('RESPONSECODE'), array("00", "60"));
    }

    public function toArray()
    {
        return $this->parameters;
    }

    public function getDataString()
    {
        return $this->dataString;
    }
}

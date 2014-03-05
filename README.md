# Sips PHP library #

This library allows you to easily implement an [Sips] integration into your project.
It provides the necessary components to complete a correct payment flow with the [Sips] platform.

Requirements:

- PHP 5.3
- network connection between your webserver and the Sips platform

As always, this is work in progress. Please feel free to fork this project and let them pull requests coming!

## Overview ##

The library complies to the [PSR-0 standard](http://www.sitepoint.com/autoloading-and-the-psr-0-standard/),
so it can be autoloaded using PSR-0 classloaders like the one in Symfony2. See autoload.php for an example.

- Create a PaymentRequest, containing all the info needed by Sips.
- Submit it to Sips (client side)
- Receive a PaymentResponse back from Sips (as a HTTP Request)

Both PaymentRequest and PaymentResponse are authenticated by comparing the SHA sign,
which is a hash of the parameters and a secret passphrase. You can create the hash using a ShaComposer.

# SHA Composer #

Sips method to generate a SHA sign:

- "Each parameter followed by the passphrase"
  This method requires you to use the following encryption method: SHA-256.

  Implementation using this library is trivial:

```php
  <?php
	use Sips\ShaComposer\AllParametersShaComposer;
	$shaComposer = new AllParametersShaComposer($passphrase);
```

# PaymentRequest #

```php
	<?php

	use Sips\Passphrase;
	use Sips\PaymentRequest;

	$passphrase = new Passphrase('passphrase-defined-in-sips-interface');
	$shaComposer = new AllParametersShaComposer($passphrase);

	$paymentRequest = new PaymentRequest($shaComposer);

	// Optionally set Sips uri, defaults to TEST account
	//$paymentRequest->setSipsUri(PaymentRequest::PRODUCTION);

	// Set various params:
	$paymentRequest->setMerchantId('123456');
    $paymentRequest->setKeyVersion('1');
    $paymentRequest->setTransactionReference($sipsTransactionReference);
    $paymentRequest->setAmount(1000);
    $paymentRequest->setCurrency('EUR');
    $paymentRequest->setLanguage('nl');
    $paymentRequest->setPaymentBrand('VISA');
	// ...

	$paymentRequest->validate();

	// Create Http client to send the paymentRequest.
	$client = new Zend_Http_Client($paymentRequest->getSipsUri());
	$client->setParameterPost('Data', $paymentRequest->toParameterString());
    $client->setParameterPost('InterfaceVersion', '<Sips interfaceVersion>');
    $client->setParameterPost('Seal', $paymentRequest->getShaSign());

    $response = $client->request(Zend_Http_Client::POST);
    echo $response->getRawBody();
    exit();

```

# PaymentResponse #

```php
  	<?php
	use Sips\PaymentResponse;
	use Sips\ShaComposer\AllParametersShaComposer;

	// ...

	$paymentResponse = new PaymentResponse($_REQUEST);

	$passphrase = new Passphrase('passphrase-defined-in-sips-interface');
	$shaComposer = new AllParametersShaComposer($passphrase);

	if($paymentResponse->isValid($shaComposer) && $paymentResponse->isSuccessful()) {
		// handle payment confirmation
	}
	else {
		// perform logic when the validation fails
	}
```

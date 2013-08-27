<?php

$loader = require 'vendor/autoload.php';

// add non PSR-0 code to include path
set_include_path( __DIR__ . '/lib' . PATH_SEPARATOR .
                    get_include_path());

// enable include path class loading
$loader->setUseIncludePath(true);
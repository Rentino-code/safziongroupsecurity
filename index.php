<?php

// Include the autoloader
require_once __DIR__ . '/autoload.php';

// Now you can use your classes without requiring them explicitly
use DHL\Entity\AM\TrackShipmentRequest;
use DHL\Client\Web as WebserviceClient;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// public/index.php

// Load configurations
$config = require(__DIR__ . '/../config/config.php');

// Include security features
include(__DIR__ . '/security.php');

// Rest of your website code...

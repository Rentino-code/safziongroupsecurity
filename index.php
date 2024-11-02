<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// public/index.php

// Load configurations
$config = require(__DIR__ . '/../config/config.php');

// Include security features
include(__DIR__ . '/security.php');

// Rest of your website code...
?>
<?php
// Load your configuration
$config = require_once '/home/safzawjz/etc/config/config.php';  // Path on hosting site

// Include the DHL autoloader or Composer autoload if you used Composer
require 'vendor/autoload.php';

use DHL\Entity\GB\ShipmentRequest;
use DHL\Client\Web as WebserviceClient;

if (isset($_POST['tracking_number'])) {
    $trackingNumber = $_POST['tracking_number'];
    
    // Initialize DHL settings
    $dhl = $config['dhl'];
    
    // Prepare the shipment request
    $sample = new ShipmentRequest();
    $sample->SiteID = $dhl['id'];
    $sample->Password = $dhl['pass'];
    
    // Set request details
    $sample->ShipmentDetails->Contents = 'Tracking shipment';
    $sample->ShipmentDetails->Date = date('Y-m-d');
    $sample->ShipmentDetails->DoorTo = 'DD';
    $sample->ShipmentDetails->CurrencyCode = 'USD';
    
    // DHL client for the staging environment
    $client = new WebserviceClient('staging');
    
    // Send the request and capture the response
    try {
        $response = $client->call($sample);
        echo $response; // You can format this or parse it to display specific details
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'No tracking number provided.';
}
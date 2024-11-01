<?php
require 'vendor/autoload.php';

use Alfallouji\DHL\DHLTracking;

// Load the configuration
$config = include '/path/to/your/config.php';

// Check if the configuration contains DHL credentials
if (!isset($config['dhl']['id']) || !isset($config['dhl']['pass'])) {
    die('DHL credentials are missing.');
}

// Initialize the DHL API client with credentials
$dhl = new DHLTracking($config['dhl']['id'], $config['dhl']['pass']);

// Use the DHL API to get tracking information
if (isset($_POST['tracking_id'])) {
    $trackingID = $_POST['tracking_id'];
    $trackingInfo = $dhl->getTrackingInfo($trackingID);

    if ($trackingInfo) {
        echo json_encode(['status' => 'success', 'data' => $trackingInfo]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tracking information not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No tracking ID provided']);
}
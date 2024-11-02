<?php
// Load your configuration and necessary classes
require(__DIR__ . '/vendor/autoload.php');
require_once __DIR__ . '/TrackShipmentRequest.php';  // Adjust path if necessary

use DHL\Client\Web as WebserviceClient;
use DHL\Entity\AM\GetQuote;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Entity\GB\ShipmentResponse;
use DHL\Datatype\AM\PieceType;
use DHL\Datatype\GB\Piece;
use DHL\Entity\Base;
use DHL\Entity\AM\TrackShipmentRequest;

// Initialize DHL settings
$config = array(
    'dhl' => array(
        'id' => 'your_dhl_site_id', // Replace with your DHL ID
        'pass' => 'your_dhl_password', // Replace with your DHL password
        'tracking number' => '1234567890', // Replace with the actual tracking number
        'shipperAccountNumber' => 'Your_Shipper_Account_Number',
        'billingAccountNumber' => 'Your_Billing_Account_Number',
        'dutyAccountNumber' => 'Your_Duty_Account_Number',
    ),
);

$dhl = $config['dhl'];

// Step 1: Get a Shipping Quote
try {
    $quoteRequest = new GetQuote();
    $quoteRequest->SiteID = $dhl['id'];
    $quoteRequest->Password = $dhl['pass'];
    $quoteRequest->MessageTime = date('Y-m-d\TH:i:sP');
    $quoteRequest->MessageReference = '1234567890123456789012345678901';
    $quoteRequest->BkgDetails->Date = date('Y-m-d');
    $quoteRequest->BkgDetails->DimensionUnit = 'CM';
    $quoteRequest->BkgDetails->WeightUnit = 'KG';
    $quoteRequest->BkgDetails->PaymentCountryCode = 'CA';
    $quoteRequest->BkgDetails->IsDutiable = 'Y';

    // Add package details
    $piece = new PieceType();
    $piece->PieceID = 1;
    $piece->Height = 10;
    $piece->Depth = 5;
    $piece->Width = 10;
    $piece->Weight = 10;
    $quoteRequest->BkgDetails->addPiece($piece);

    // Set origin and destination details
    $quoteRequest->From->CountryCode = 'CA';
    $quoteRequest->From->Postalcode = 'H3E1B6';
    $quoteRequest->From->City = 'Montreal';
    $quoteRequest->To->CountryCode = 'CH';
    $quoteRequest->To->Postalcode = '1226';
    $quoteRequest->To->City = 'Thonex';
    $quoteRequest->Dutiable->DeclaredValue = '100.00';
    $quoteRequest->Dutiable->DeclaredCurrency = 'CHF';

    // Call DHL API for the quote
    $responseXml = $client->call($quoteRequest);
    echo "<h2>Shipping Quote Response:</h2>";
    echo "<pre>" . htmlspecialchars($responseXml) . "</pre>";

} catch (Exception $e) {
    echo 'Error in quote request: ' . $e->getMessage();
}

// Step 2: Tracking Functionality
$trackingNumber = '1234567890';  // Replace with the actual tracking number

// Initialize the TrackShipmentRequest with required parameters
$trackingRequest = new TrackShipmentRequest($dhl['id'], $dhl['pass'], $trackingNumber);
$xmlRequest = $trackingRequest->toXML(); // This will generate the XML string

// Initialize DHL API client
$client = new WebserviceClient('staging');

try {
    // Send the tracking request to the DHL API using the TrackShipmentRequest object
    $trackingResponseXml = $client->call($trackingRequest); // Pass the object directly
    echo "<h2>Tracking Information for Shipment {$trackingNumber}:</h2>";
    echo "<pre>" . htmlspecialchars($trackingResponseXml) . "</pre>";
} catch (Exception $e) {
    echo 'Error in tracking request: ' . $e->getMessage();
}

// Step 3: Track a Shipment
function trackShipment($trackingNumber)
{
    
    global $client, $dhl;
    try {
        $trackingRequest = new TrackShipmentRequest();
        $trackingRequest->SiteID = $dhl['id'];
        $trackingRequest->Password = $dhl['pass'];
        $trackingRequest->TrackingNumber = $trackingNumber;

        $trackingResponseXml = $client->call($trackingRequest);
        echo "<h2>Tracking Information for Shipment {$trackingNumber}:</h2>";
        echo "<pre>" . htmlspecialchars($trackingResponseXml) . "</pre>";

    } catch (Exception $e) {
        echo 'Error in tracking request: ' . $e->getMessage();
    }
}

// Test tracking
$trackingNumber = '1234567890';  // Replace with a real tracking number
trackShipment($trackingNumber);

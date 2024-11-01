<?php
use DHL\Entity\AM\GetQuote;
use DHL\Datatype\AM\PieceType;
use DHL\Client\Web as WebserviceClient;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Entity\GB\ShipmentResponse;
use DHL\Datatype\GB\Piece;
use DHL\Datatype\GB\SpecialService;

// Include autoloader for DHL and other necessary files
require(__DIR__ . '/vendor/autoload.php');

// DHL settings
$config = array(
    'dhl' => array(
        'id' => 'rentino744@gmail.com', // Replace with your DHL ID
        'pass' => 'KauAc/V/@Wj/5p3', // Replace with your DHL password
        'shipperAccountNumber' => 'Your_Shipper_Account_Number',
        'billingAccountNumber' => 'Your_Billing_Account_Number',
        'dutyAccountNumber' => 'Your_Duty_Account_Number',
    ),
);
$dhl = $config['dhl'];

// Step 1: Get a Shipping Quote
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
$client = new WebserviceClient('staging');
$responseXml = $client->call($quoteRequest);
echo "<h2>Shipping Quote Response:</h2>";
echo "<pre>" . htmlspecialchars($responseXml) . "</pre>";

// Step 2: Request a Shipment Label (PDF output)
$shipmentRequest = new ShipmentRequest();
$shipmentRequest->SiteID = $dhl['id'];
$shipmentRequest->Password = $dhl['pass'];
$shipmentRequest->MessageTime = date('Y-m-d\TH:i:sP');
$shipmentRequest->MessageReference = '1234567890123456789012345678901';
$shipmentRequest->RegionCode = 'AM';
$shipmentRequest->Billing->ShipperAccountNumber = $dhl['shipperAccountNumber'];
$shipmentRequest->Billing->BillingAccountNumber = $dhl['billingAccountNumber'];
$shipmentRequest->Billing->DutyAccountNumber = $dhl['dutyAccountNumber'];
$shipmentRequest->Consignee->CompanyName = 'Company ABC';
$shipmentRequest->Consignee->addAddressLine('123 Example St');
$shipmentRequest->Consignee->City = 'Thonex';
$shipmentRequest->Consignee->PostalCode = '1226';
$shipmentRequest->Consignee->CountryCode = 'CH';
$shipmentRequest->Consignee->Contact->PersonName = 'John Doe';
$shipmentRequest->Consignee->Contact->PhoneNumber = '1234567890';
$shipmentRequest->ShipmentDetails->NumberOfPieces = 1;
$shipmentRequest->ShipmentDetails->Weight = '10.0';
$shipmentRequest->ShipmentDetails->WeightUnit = 'KG';
$shipmentRequest->ShipmentDetails->GlobalProductCode = 'P';
$shipmentRequest->ShipmentDetails->LocalProductCode = 'P';
$shipmentRequest->ShipmentDetails->Date = date('Y-m-d');
$shipmentRequest->ShipmentDetails->CurrencyCode = 'USD';

// Add a single piece for the shipment
$piece = new Piece();
$piece->PieceID = '1';
$piece->PackageType = 'EE';
$piece->Weight = '10.0';
$shipmentRequest->ShipmentDetails->addPiece($piece);

// Call DHL API to request the shipment and get the label
$shipmentResponseXml = $client->call($shipmentRequest);
$shipmentResponse = new ShipmentResponse();
$shipmentResponse->initFromXML($shipmentResponseXml);

// Display or store the PDF label
if (isset($shipmentResponse->LabelImage->OutputImage)) {
    $labelData = base64_decode($shipmentResponse->LabelImage->OutputImage);
    file_put_contents('dhl-label.pdf', $labelData);

    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($labelData));
    echo $labelData;
} else {
    echo "<h2>No Label Found in Response.</h2>";
}

// Step 3: Track a Shipment (Replace with track.php functionality)
function trackShipment($trackingNumber) {
    global $dhl;
    $trackingClient = new WebserviceClient('staging');  // Assuming WebserviceClient can handle tracking as well
    $trackingRequest = new TrackShipmentRequest();      // Adjust class name if necessary
    
    $trackingRequest->SiteID = $dhl['id'];
    $trackingRequest->Password = $dhl['pass'];
    $trackingRequest->TrackingNumber = $trackingNumber;

    $trackingResponseXml = $trackingClient->call($trackingRequest);
    echo "<h2>Tracking Information for Shipment {$trackingNumber}:</h2>";
    echo "<pre>" . htmlspecialchars($trackingResponseXml) . "</pre>";
}

// Example tracking number
$trackingNumber = '1234567890';  // Replace with a real tracking number
trackShipment($trackingNumber);

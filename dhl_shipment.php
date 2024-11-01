<?php
// Use necessary classes from the DHL API
use DHL\Entity\GB\ShipmentResponse;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Client\Web as WebserviceClient;
use DHL\Datatype\GB\Piece;
use DHL\Datatype\GB\SpecialService;

// Include your initialization or autoloader script
require(__DIR__ . '/init.php');

// DHL settings
$dhl = $config['dhl'];

// Create a new ShipmentRequest
$sample = new ShipmentRequest();

// Set DHL credentials
$sample->SiteID = $dhl['id'];
$sample->Password = $dhl['pass'];

// Set values for the shipment request
$sample->MessageTime = date('Y-m-d\TH:i:sP'); // Current time
$sample->MessageReference = '1234567890123456789012345678901'; // Reference
$sample->RegionCode = 'AM'; // Region
$sample->RequestedPickupTime = 'Y'; // Pickup time
$sample->NewShipper = 'Y'; // New shipper
$sample->LanguageCode = 'en'; // Language
$sample->PiecesEnabled = 'Y'; // Pieces enabled

// Billing details
$sample->Billing->ShipperAccountNumber = $dhl['shipperAccountNumber'];
$sample->Billing->ShippingPaymentType = 'S';
$sample->Billing->BillingAccountNumber = $dhl['billingAccountNumber'];
$sample->Billing->DutyPaymentType = 'S';
$sample->Billing->DutyAccountNumber = $dhl['dutyAccountNumber'];

// Consignee details
$sample->Consignee->CompanyName = 'Ssense';
$sample->Consignee->addAddressLine('333 Chabanel West, #900');
$sample->Consignee->City = 'Montreal';
$sample->Consignee->PostalCode = 'H3E1G6';
$sample->Consignee->CountryCode = 'CA';
$sample->Consignee->Contact->PersonName = 'Bashar Al-Fallouji';
$sample->Consignee->Contact->PhoneNumber = '0435 336 653';
$sample->Consignee->Contact->Email = 'bashar@alfallouji.com';

// Commodity details
$sample->Commodity->CommodityCode = 'cc';
$sample->Commodity->CommodityName = 'cn';
$sample->Dutiable->DeclaredValue = '200.00';
$sample->Dutiable->DeclaredCurrency = 'USD';

// Shipment details
$sample->ShipmentDetails->NumberOfPieces = 2;
$piece = new Piece();
$piece->PieceID = '1';
$piece->PackageType = 'EE';
$piece->Weight = '5.0';
$piece->DimWeight = '600.0';
$piece->Width = '50';
$piece->Height = '100';
$piece->Depth = '150';
$sample->ShipmentDetails->addPiece($piece);

// Add second piece
$piece = new Piece();
$piece->PieceID = '2';
$piece->PackageType = 'EE';
$piece->Weight = '5.0';
$piece->DimWeight = '600.0';
$piece->Width = '50';
$piece->Height = '100';
$piece->Depth = '150';
$sample->ShipmentDetails->addPiece($piece);

// Additional shipment details
$sample->ShipmentDetails->Weight = '10.0';
$sample->ShipmentDetails->WeightUnit = 'L';
$sample->ShipmentDetails->GlobalProductCode = 'P';
$sample->ShipmentDetails->LocalProductCode = 'P';
$sample->ShipmentDetails->Date = date('Y-m-d');
$sample->ShipmentDetails->Contents = 'AM international shipment contents';
$sample->ShipmentDetails->DoorTo = 'DD';
$sample->ShipmentDetails->DimensionUnit = 'I';
$sample->ShipmentDetails->InsuredAmount = '1200.00';
$sample->ShipmentDetails->PackageType = 'EE';
$sample->ShipmentDetails->IsDutiable = 'Y';
$sample->ShipmentDetails->CurrencyCode = 'USD';

// Shipper details
$sample->Shipper->ShipperID = '751008818';
$sample->Shipper->CompanyName = 'IBM Corporation';
$sample->Shipper->RegisteredAccount = '751008818';
$sample->Shipper->addAddressLine('1 New Orchard Road');
$sample->Shipper->City = 'New York';
$sample->Shipper->PostalCode = '10504';
$sample->Shipper->CountryCode = 'US';

// Add special services
$specialService = new SpecialService();
$specialService->SpecialServiceType = 'A';
$sample->addSpecialService($specialService);

$specialService = new SpecialService();
$specialService->SpecialServiceType = 'I';
$sample->addSpecialService($specialService);

// Set other necessary parameters
$sample->EProcShip = 'N';
$sample->LabelImageFormat = 'PDF';

// Call the DHL XML API
$client = new WebserviceClient('staging');
$xml = $client->call($sample);
$response = new ShipmentResponse();
$response->initFromXML($xml);

// Store the label as a PDF
if (isset($response->LabelImage->OutputImage)) {
    $labelData = base64_decode($response->LabelImage->OutputImage);
    file_put_contents('dhl-label.pdf', $labelData); // Save as PDF

    // Display PDF in the browser
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($labelData));
    echo $labelData;
} else {
    echo "<h2>No Label Found in Response.</h2>";
}

// Optionally, display the XML response for debugging
echo "<h2>XML Response:</h2>";
echo "<pre>" . htmlspecialchars($xml) . "</pre>";
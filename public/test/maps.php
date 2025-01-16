<?php
header('Content-Type: application/json');
ini_set('memory_limit', '8192M'); 
// Get the boundary data from the request body
$boundaryJson = file_get_contents('php://input');

if (!$boundaryJson) {
    $error = json_encode(['error' => 'No data received']);
    echo $error;
    exit();
}

$main_array = json_decode($boundaryJson, true);

$m = $main_array['features'][0]['geometry']['coordinates'][0];
$realMinLat = min(array_column($m, 0));
$realMaxLat = max(array_column($m, 0));
$realMinLong = min(array_column($m, 1));
$realMaxLong = max(array_column($m, 1));

// Define the area of interest
$areaOfInterest = [
    'latMin' => $realMinLat,
    'latMax' => $realMaxLat,
    'longMin' => $realMinLong,
    'longMax' => $realMaxLong
];

// Generate square coordinates
function generateSquare($topLeftLat, $topLeftlong, $sideLength) {
    $halfSide = $sideLength / 2;
    $quarterTurn = M_PI / 2;
//long - это вертикаль
//lat - горизонталь
    $topLeft = ['lat' => $topLeftLat,              'long' => $topLeftlong];
    $topRight = ['lat' => $topLeftLat + $halfSide, 'long' => $topLeftlong];

    $bottomRight = ['lat' => $topLeftLat + $halfSide, 'long' => $topLeftlong + $halfSide/2];
    $bottomLeft = ['lat' => $topLeftLat, 'long' => $topLeftlong + $halfSide/2];

    return [$topLeft, $topRight, $bottomRight, $bottomLeft, $topLeft];
}

// Initialize array to store polygons
$polygonArray = [];

// Set initial values
$currentLat1 = $realMinLat;
$currentLong1 = $realMinLong;
$sideLength = 0.001;

// Move horizontally
while ($currentLong1 <= $realMaxLong) {
    // Generate squares for current latitude
    while ($currentLat1 <= $realMaxLat) {
        $polygonArray[] = generateSquare($currentLat1, $currentLong1, $sideLength);
        $currentLat1 += $sideLength/2;
    }
    
    // Move to next longitude
    $currentLong1 += $sideLength/4;
    $currentLat1 = $realMinLat;
}

// Output JSON
header('Content-Type: application/json');
//file_put_contents("last_markers.json",json_encode($polygonArray));
echo json_encode($polygonArray);

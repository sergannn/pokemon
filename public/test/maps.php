<?php

header('Content-Type: application/json');
ini_set('memory_limit', '8192M');

// Получаем данные границы из тела запроса
$boundaryJson = file_get_contents('php://input');
$isInitRequest = isset($_GET['init']);
$currentBatch = isset($_GET['batch']) ? intval($_GET['batch']) : 0;
$batchSize = isset($_GET['batchSize']) ? intval($_GET['batchSize']) : 1000; // Увеличим размер батча

if (!$boundaryJson) {
    echo json_encode(['error' => 'No data received']);
    exit();
}

$main_array = json_decode($boundaryJson, true);

if (!isset($main_array['features'][0]['geometry']['coordinates'][0])) {
    echo json_encode(['error' => 'Invalid GeoJSON format']);
    exit();
}

$polygon = $main_array['features'][0]['geometry']['coordinates'][0];

// Функция для проверки, находится ли точка внутри многоугольника
function isPointInPolygon($point, $polygon) {
    $x = $point['lat'];
    $y = $point['long'];
    $inside = false;
    $n = count($polygon);
    $p1 = $polygon[0];
    
    for ($i = 1; $i <= $n; $i++) {
        $p2 = $polygon[$i % $n];
        if ($y > min($p1[1], $p2[1])) {
            if ($y <= max($p1[1], $p2[1])) {
                if ($x <= max($p1[0], $p2[0])) {
                    if ($p1[1] != $p2[1]) {
                        $xinters = ($y - $p1[1]) * ($p2[0] - $p1[0]) / ($p2[1] - $p1[1]) + $p1[0];
                    }
                    if ($p1[0] == $p2[0] || $x <= $xinters) {
                        $inside = !$inside;
                    }
                }
            }
        }
        $p1 = $p2;
    }
    return $inside;
}

// Генерация квадрата

function generateSquare($topLeftLat, $topLeftLong, $sideLength) {
    $square = [
        ['lat' => $topLeftLat, 'long' => $topLeftLong],    // Верхняя левая
        ['lat' => $topLeftLat + $sideLength, 'long' => $topLeftLong],    // Верхняя правая
        ['lat' => $topLeftLat + $sideLength, 'long' => $topLeftLong + $sideLength/2],    // Нижняя правая
        ['lat' => $topLeftLat, 'long' => $topLeftLong + $sideLength/2],    // Нижняя левая
        ['lat' => $topLeftLat, 'long' => $topLeftLong]    // Верхняя левая (замыкает полигон)
    ];
    return $square;
}

// Рассчитываем границы области
$realMinLat = min(array_column($polygon, 0));
$realMaxLat = max(array_column($polygon, 0));
$realMinLong = min(array_column($polygon, 1));
$realMaxLong = max(array_column($polygon, 1));

// Параметры сетки
$sideLength = 0.01;
$latSteps = ceil(($realMaxLat - $realMinLat) / ($sideLength*1)); //sidelength отражается на заполнении по гориз
//$latSteps = ceil(($realMaxLat - $realMinLat) / $sideLength);
$longSteps = ceil(($realMaxLong - $realMinLong) / $sideLength*2);
$totalCells = $latSteps * $longSteps;

if ($isInitRequest) {
    echo json_encode([
        'total' => $totalCells,
        'bounds' => [
            'minLat' => $realMinLat,
            'maxLat' => $realMaxLat,
            'minLong' => $realMinLong,
            'maxLong' => $realMaxLong
        ],
        'gridParams' => [
            'sideLength' => $sideLength,
            'latSteps' => $latSteps,
            'longSteps' => $longSteps
        ]
    ]);
    exit();
}

// Вычисляем диапазон ячеек для текущего батча
$startCell = $currentBatch * $batchSize;
$endCell = min($startCell + $batchSize, $totalCells);

// Если мы вышли за пределы, возвращаем пустой результат
if ($startCell >= $totalCells) {
    echo json_encode([
        'polygons' => [],
        'batch' => $currentBatch,
        'processedCells' => 0,
        'totalProcessed' => $totalCells,
        'totalCells' => $totalCells,
        'progress' => 100,
        'complete' => true
    ]);
    exit();
}

$polygonArray = [];



// Обрабатываем только нужный диапазон ячеек
for ($cellIndex = $startCell; $cellIndex < $endCell; $cellIndex++) {
    $latIndex = $cellIndex % $latSteps;
    $longIndex = (int)($cellIndex / $latSteps);
    
//    $currentLat = $realMinLat + ($latIndex * $sideLength);
    $currentLat = $realMinLat + ($latIndex * ($sideLength));
    $currentLong = $realMinLong + ($longIndex * $sideLength*0.5);
    
    // Генерируем квадрат с учётом корректировки широты
    $square = generateSquare(
        $currentLat,
        $currentLong,
        $sideLength
    );
    
    // Проверяем центр квадрата
    $centerLat = $currentLat + ($sideLength / 2);
    $centerLong = $currentLong + ($sideLength / 2);
    $centerPoint = ['lat' => $centerLat, 'long' => $centerLong];
    
    if (isPointInPolygon($centerPoint, $polygon)) {
        $polygonArray[] = $square;
    }
}


$fileName = 'result_' . date('Ymd_His') . '.geojson';
if (saveGeoJSONToFile($fileName, $polygonArray)) {
    echo json_encode([
        'polygons' => $polygonArray,
        'batch' => $currentBatch,
        'processedCells' => count($polygonArray),
        'totalProcessed' => $endCell,
        'totalCells' => $totalCells,
        'progress' => round(($endCell / $totalCells) * 100, 2),
        'complete' => $endCell >= $totalCells,
        'savedFile' => $fileName
    ]);
} else {
    echo json_encode([
        'error' => 'Failed to save GeoJSON file',
        'batch' => $currentBatch,
        'processedCells' => count($polygonArray),
        'totalProcessed' => $endCell,
        'totalCells' => $totalCells,
        'progress' => round(($endCell / $totalCells) * 100, 2),
        'complete' => $endCell >= $totalCells
    ]);
}
function saveGeoJSONToFile($fileName, $data) {
    $geojson = [
        'type' => 'FeatureCollection',
        'features' => array_map(function($polygon) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [$polygon]
                ]
            ];
        }, $data)
    ];

    $json = json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($fileName, $json) !== false) {
        return true;
    }
    return false;
}
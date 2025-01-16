<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View;

use Illuminate\Http\Request;

 class CoordinateImportController
{
    public function import(Request $request)
    {ini_set('memory_limit', '8192M'); 
        ini_set('max_execution_time', '-1');
            $json_data = json_decode(file_get_contents('test/last_markers.json'), true);
          //  echo 123; exit();
          foreach ($json_data as $polygon) {
            // Debug message
            echo "Processing polygon: " . $polygon[0]['lat'] . "_" . $polygon[0]['long'] . "\n";
        
            $existingMarker = Marker::where([
                'lat1' => $polygon[0]['long'],
                'lon1' => $polygon[0]['lat'],
                'lat2' => $polygon[1]['long'],
                'lon2' => $polygon[1]['lat'],
                'lat3' => $polygon[2]['long'],
                'lon3' => $polygon[2]['lat'],
                'lat4' => $polygon[3]['long'],
                'lon4' => $polygon[3]['lat']
            ])->first();
        
            if ($existingMarker) {
                echo "Updated existing marker: " . $polygon[0]['lat'] . "_" . $polygon[0]['long'] . "\n";
            } else {
                Marker::create([
                    'title' => 'Polygon ' . $polygon[0]['lat'] . '_' . $polygon[0]['long'],
                    'lat1' => $polygon[0]['long'],
                    'lon1' => $polygon[0]['lat'],
                    'lat2' => $polygon[1]['long'],
                    'lon2' => $polygon[1]['lat'],
                    'lat3' => $polygon[2]['long'],
                    'lon3' => $polygon[2]['lat'],
                    'lat4' => $polygon[3]['long'],
                    'lon4' => $polygon[3]['lat']
                ]);
                echo "Created new marker: " . $polygon[0]['lat'] . "_" . $polygon[0]['long'] . "\n";
            }
        
      
        }
        return response()->json(['success' => 'Data imported successfully']);
    }
    public static function sendProgressUpdate($processedCount, $totalMarkers)
    {
        $response = new \Illuminate\Http\JsonResponse([
            'progress' => round(($processedCount / $totalMarkers) * 100),
            'message' => 'Processing markers...'
        ]);
        
        event(new \App\Events\ProgressUpdated($response));
    }
}
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Add polygons to a map using a GeoJSON source</title>
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
<link href="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.js"></script>
<style>
body { margin: 0; padding: 0; }
#map { position: absolute; top: 0; bottom: 0; width: 100%; }
</style>
</head>
<body>
<div id="map"></div>
<script>
const accessToken = 'pk.eyJ1Ijoic2VyZ2Fubm4iLCJhIjoiY2w2NWw0ejZ3MDdmZDNpbm84eWtqOWx0cSJ9.KdhQrNoti2fgGSRSqDiyHQ';
async function initMap() {
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/standard',  
        //style: 'mapbox://styles/mapbox/light-v11',
        center: [30.3352, 59.9346], // New center coordinates for Saint Petersburg
        zoom: 12,
        accessToken: accessToken
    });
var clickedStateId=null;
    await map.on('load', async () => {
        try {
            const boundaryResponse= await fetch('https://api.geoapify.com/v2/place-details?id=51fa0b3d62f4503e4059b6dac35e28f84d40f00101f9018f6c060000000000c002089203105361696e742050657465727362757267&apiKey=b8568cb9afc64fad861a69edbddb2658');
            console.log(boundaryResponse);
            const boundaryData = await boundaryResponse.json();
            console.log(boundaryData);
            const boundaryJson = JSON.stringify(boundaryData);
            const response = await fetch("/test/maps.php", {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: boundaryJson
});
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            console.log('Received data:', data);

            const polygons = [];
            console.log(data);
            for (let i = 0; i < data.length; i++) {
                const polygon = data[i].map(point => [point.lat, point.long]);
                polygons.push(polygon);
            }
            console.log('Created polygons:', polygons);

            map.addSource('polygons', {
                'type': 'geojson',
                'data': {
                    'type': 'FeatureCollection',
                    'features': []
                },
                'generateId': true
            });
         /*   map.addSource('polygons2', {
                'type': 'geojson',
                'data': {
                    'type': 'FeatureCollection',
                    'features': []
                }
            });
            map.addLayer({
                'id': 'polygons2',
                'type': 'fill',
                'source': 'polygons2',
                'layout': {},
                'paint': {
                    'fill-color': 'gray',
                    'fill-opacity': 0.5
                }
            });*/
            map.addLayer({
                'id': 'polygons',
                'type': 'fill',
                'source': 'polygons',
                'layout': {},
                'paint': {
                'fill-color': [
                    'case',
                    ['boolean', ['feature-state', 'click'], false],
                    'red',
                    '#888888'
                ]
            }
            });
            map.addLayer({
            'id': 'polygons_borders',
            'type': 'line',
            'source': 'polygons',
            'layout': {},
            'paint': {
                'line-color': '#627BC1',
                'line-width': 1
            }
        });    
            console.log('Number of polygons:', polygons.length);

            const features = polygons.map((polygon, index) => ({
                id:1,
                type: 'Feature',
                featureId:0,
               properties: { id: index},
                geometry: {
                    type: 'Polygon',
                    coordinates: [polygon]
                }
            }));

            map.getSource('polygons').setData({
                type: 'FeatureCollection',
                features: features
            });
           // map.getSource('polygons2').setData(boundaryData);
            console.log('boundary Data successfully added to source');
            window.sermap = map;
            map.on('click', 'polygons', function(e) {
                console.log("click");
                console.log(e.features);
            if (e.features.length > 0) {
                if (clickedStateId) {
                    map.setFeatureState(
                        { source: 'polygons', id: clickedStateId },
                        { click: false }
                    );
                }
                console.log(e.features[0]);
                clickedStateId = e.features[0].properties.id;
                console.log(clickedStateId);
                map.setFeatureState(
                    { source: 'polygons', id: clickedStateId },
                    { click: true }
                );
            }
        });
        } catch (error) {
            console.error('Error initializing map:', error);
        }
    });
}

window.onload = initMap;
</script>
</body>
</html>

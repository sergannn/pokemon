<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Add polygons to a map using a GeoJSON source</title>
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
<link href="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<style>
body { margin: 0; padding: 0; }
#map { position: absolute; top: 0; bottom: 0; width: 100%; }

.wrapper{
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100vw;
  text-align:center;
  transform: translateX(-50%);
}

.spanner{
  position:absolute;
  top: 50%;
  left: 0;
  background: #2a2a2a;
  width: 100%;
  display:block;
  text-align:center;
  height: 300px;
  color: #FFF;
  transform: translateY(-50%);
  z-index: 1000;
  visibility: hidden;
}

.overlay{
  position: fixed;
	width: 100%;
	height: 100%;
  background: rgba(0,0,0,0.5);
  visibility: hidden;
}

.loader,
.loader:before,
.loader:after {
  border-radius: 50%;
  width: 2.5em;
  height: 2.5em;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
  -webkit-animation: load7 1.8s infinite ease-in-out;
  animation: load7 1.8s infinite ease-in-out;
}
.loader {
  color: #ffffff;
  font-size: 10px;
  margin: 80px auto;
  position: relative;
  text-indent: -9999em;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation-delay: -0.16s;
  animation-delay: -0.16s;
}
.loader:before,
.loader:after {
  content: '';
  position: absolute;
  top: 0;
}
.loader:before {
  left: -3.5em;
  -webkit-animation-delay: -0.32s;
  animation-delay: -0.32s;
}
.loader:after {
  left: 3.5em;
}
@-webkit-keyframes load7 {
  0%,
  80%,
  100% {
    box-shadow: 0 2.5em 0 -1.3em;
  }
  40% {
    box-shadow: 0 2.5em 0 0;
  }
}
@keyframes load7 {
  0%,
  80%,
  100% {
    box-shadow: 0 2.5em 0 -1.3em;
  }
  40% {
    box-shadow: 0 2.5em 0 0;
  }
}

.show{
  visibility: visible;
}

.spanner, .overlay{
	opacity: 0;
	-webkit-transition: all 0.3s;
	-moz-transition: all 0.3s;
	transition: all 0.3s;
}

.spanner.show, .overlay.show {
	opacity: 1
}
</style>
</head>
<body>
<div class="wrapper">
<div class="overlay"></div>
<div class="spanner">
  <div class="loader"></div>
  <p>Карта загружается...</p>
</div>
<div id="map"></div>
<script>
const accessToken = 'pk.eyJ1Ijoic2VyZ2Fubm4iLCJhIjoiY2w2NWw0ejZ3MDdmZDNpbm84eWtqOWx0cSJ9.KdhQrNoti2fgGSRSqDiyHQ';
async function initMap() {
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/standard',  
        //style: 'mapbox://styles/mapbox/light-v11',
        center: [30.3352, 59.9346], // New center coordinates for Saint Petersburg
        zoom: 18,
        accessToken: accessToken
    });
var clickedStateId=null;
    await map.on('load', async () => {
        try {
            $("div.spanner").addClass("show");
            $("div.overlay").addClass("show");
//          const response = await fetch("/test/last_markers.json");
const response = await fetch("/webmarkers");
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            console.log('Received data:', data);

            const polygons = [];
            console.log(data);
            data.forEach(function(pp,index) 
            {//console.log(pp.lat1,pp.lon1,pp.lat2,pp.lon2,pp.lat3,pp.lon3,pp.lat4,pp.lon4,index);
                let polygon = [
                    [pp.lon1, pp.lat1],
                    [pp.lon2, pp.lat2],
                    [pp.lon3, pp.lat3],
                    [pp.lon4, pp.lat4],
                    [pp.lon1, pp.lat1],
                ];
                polygons.push(polygon);
            });
            console.log('Created polygons:', polygons);
            $("div.spanner").removeClass("show");
            $("div.overlay").removeClass("show");
            $("div.wrapper").removeClass("wrapper");
            map.flyTo({
            center: [polygons[0][0],polygons[0][1]],
            essential: true // this animation is considered essential with respect to prefers-reduced-motion
        });
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
                type: 'Feature',
               properties: { id: index},
                geometry: {
                    type: 'Polygon',
                    coordinates: [polygon]
                },
               // generateId:true
            }));
            console.log(features);
            map.getSource('polygons').setData({
                type: 'FeatureCollection',
                features: features
            });
           // map.getSource('polygons2').setData(boundaryData);
            console.log('boundary Data successfully added to source');
            window.sermap = map;
            map.on('click', 'polygons',async  function(e) {
                console.log("click");
                console.log(e.features);
            if (e.features.length > 0) {
               /* if (clickedStateId) {
                    map.setFeatureState(
                        { source: 'polygons', id: clickedStateId },
                        { click: false }
                    );
                }*/
                console.log(e.features[0]);
                clickedStateId = e.features[0].properties.id;
                console.log(clickedStateId);
                const response = await fetch(`/markers/${clickedStateId}/presents`);
               // console.log(response);
                const data = await response.json();
                console.log(data);
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

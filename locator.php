<?php
$lat=$_GET["lat"];
$lon=$_GET["lon"];
$r=$_GET["r"];

function geoJson ($locales)
    {
        $original_data = json_decode($locales, true);
        $features = array();

        foreach($original_data['stops'] as $key => $value) {
            $features[] = array(
                    'type' => 'Feature',
                    'geometry' => array('type' => 'Point', 'coordinates' => array((float)$value['geometry']['coordinates'][0],(float)$value['geometry']['coordinates'][1])),
                    'properties' => array('name' => $value['name'], 'id' => $value['onestop_id']),
                    );
            };

        $allfeatures = array('type' => 'FeatureCollection', 'features' => $features);
        return json_encode($allfeatures, JSON_PRETTY_PRINT);

    }
$url = 'https://transit.land/api/v1/stops?lon='.$lon.'&lat='.$lat.'&r='.$r."&per_page=200";
$file = "mappa.json";

$src = fopen($url, 'r');
$dest = fopen($file, 'w');
stream_copy_to_stream($src, $dest);

$file1 = "mappaf.json";
$original_json_string = file_get_contents('mappa.json', true);
if($original_json_string=="[]")
{
  echo "<script type='text/javascript'>alert('Non ci sono fermate vicino alla tua posizione');</script>";

}
$dest1 = fopen($file1, 'w');

$geostring=geoJson($original_json_string);

fputs($dest1, $geostring);


?>

<!DOCTYPE html>
<html lang="it">
  <head>
  <title>Trasporti Pa-Na-Roma-Tr</title>
  <link rel="stylesheet" href="http://necolas.github.io/normalize.css/2.1.3/normalize.css" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
  <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.css" />
   <link rel="stylesheet" href="http://turbo87.github.io/leaflet-sidebar/src/L.Control.Sidebar.css" />
   <link href="https://fonts.googleapis.com/css?family=Titillium+Web" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="MarkerCluster.css" />
        <link rel="stylesheet" href="MarkerCluster.Default.css" />
        <meta property="og:image" content="http://www.piersoft.it/panarotre/bus_.png"/>
  <script src="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>
<script src="http://turbo87.github.io/leaflet-sidebar/src/L.Control.Sidebar.js"></script>
   <script src="leaflet.markercluster.js"></script>
<script type="text/javascript">

function microAjax(B,A){this.bindFunction=function(E,D){return function(){return E.apply(D,[D])}};this.stateChange=function(D){if(this.request.readyState==4 ){this.callbackFunction(this.request.responseText)}};this.getRequest=function(){if(window.ActiveXObject){return new ActiveXObject("Microsoft.XMLHTTP")}else { if(window.XMLHttpRequest){return new XMLHttpRequest()}}return false};this.postBody=(arguments[2]||"");this.callbackFunction=A;this.url=B;this.request=this.getRequest();if(this.request){var C=this.request;C.onreadystatechange=this.bindFunction(this.stateChange,this);if(this.postBody!==""){C.open("POST",B,true);C.setRequestHeader("X-Requested-With","XMLHttpRequest");C.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");C.setRequestHeader("Connection","close")}else{C.open("GET",B,true)}C.send(this.postBody)}};

</script>
  <style>
  #mapdiv{
        position:fixed;
        top:0;
        right:0;
        left:0;
        bottom:0;
        font-family: Titillium Web, Arial, Sans-Serif;
}
#infodiv{
background-color: rgba(255, 255, 255, 0.95);

font-family: Titillium Web, Arial, Sans-Serif;
padding: 2px;


font-size: 10px;
bottom: 13px;
left:0px;


max-height: 50px;

position: fixed;

overflow-y: auto;
overflow-x: hidden;
}
#loader {
    position:absolute; top:0; bottom:0; width:100%;
    background:rgba(255, 255, 255, 0.9);
    transition:background 1s ease-out;
    -webkit-transition:background 1s ease-out;

}
#loader.done {
    background:rgba(255, 255, 255, 0);
}
#loader.hide {
    display:none;
}
#loader .message {
    position:absolute;
    left:50%;
    top:50%;
}
p.pic {
    width: 48px;
    margin-right: auto;
    margin-left: 18px;
}

        .lorem {
            font-style: Titillium Web;
            color: #AAA;
        }
</style>
  </head>

<body>

  <div data-tap-disabled="true">

 <div id="mapdiv"></div>
 <div id="sidebar">

</div>
<div id="infodiv" style="leaflet-popup-content-wrapper">
  <p><b>Trasporti Palermo-Napoli-Roma-Trento (Massimo 200 fermate nelle vicinanze di <?php echo $r."mt"; ?>)<br></b>
  Mappa con fermate, linee e orarie dei Bus delle aziende TPL di Napoli, Palermo, Roma, Trento by @piersoft. Fonte dati e licenze: <a href="http://blog.spaziogis.it/2016/03/02/transiland-per-mettere-insieme-e-dare-vita-ai-dati-sui-trasporti/">Transitland, per mettere insieme e "dare vita" ai dati sui trasporti</a></p>
</div>
<div id='loader'><span class='message'>loading<p class="pic"><img src="http://www.piersoft.it/panarotre/ajax-loader.gif"></p></span></div>
</div>
<script type="text/javascript">
var urlj="";
var corse=0;
function MostrarVideo(idYouTube)
{
  sidebar.show();

var contenedor = document.getElementById('sidebar');
if(idYouTube == '')
{contenedor.innerHTML = '';
} else{
var url = urlj;
contenedor.innerHTML = '<iframe width="100%" height="600" src="orari.php?id='+ url +'" frameborder="0" allowfullscreen></iframe>';
var element = document.getElementById("infodiv");
if (element !=null) element.parentNode.removeChild(element);
}
finishedLoadinglong(corse);
}
</script>
<script language="javascript" type="text/javascript">
<!--

// -->
</script>
  <script type="text/javascript">
		var lat='<?php printf($_GET['lat']); ?>',
        lon='<?php printf($_GET['lon']); ?>',
        zoom=14;


        var transport = new L.TileLayer('http://{s}.tile.thunderforest.com/transport/{z}/{x}/{y}.png', {minZoom: 0, maxZoom: 20, attribution: 'Map Data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'});
        var realvista = L.tileLayer.wms("http://213.215.135.196/reflector/open/service?", {
            		layers: 'rv1',
            		format: 'image/jpeg',attribution: '<a href="http://www.realvista.it/website/Joomla/" target="_blank">RealVista &copy; CC-BY Tiles</a> | <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
            	});

        var osm = new L.TileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {maxZoom: 20, attribution: 'Map Data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'});
		    var mapquest = new L.TileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {subdomains: '1234', maxZoom: 18, attribution: 'Map Data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'});

        var map = new L.Map('mapdiv', {
            editInOSMControl: true,
            editInOSMControlOptions: {
                position: "topright"
            },
            center: new L.LatLng(lat, lon),
            zoom: zoom,
            layers: [transport]
        });

        var baseMaps = {
    "Trasporti": transport,
    "Mapnik": osm,
    "Mapquest Open": mapquest,
    "RealVista": realvista
        };

    var sidebar = L.control.sidebar('sidebar', {
          closeButton: true,
          position: 'left'
      });
      map.addControl(sidebar);

        L.control.layers(baseMaps).addTo(map);
        var markeryou = L.marker([parseFloat('<?php printf($_GET['lat']); ?>'), parseFloat('<?php printf($_GET['lon']); ?>')]).addTo(map);
        markeryou.bindPopup("<b>Sei qui</b>");
       var ico=L.icon({iconUrl:'icobusstop.png', iconSize:[40,60],iconAnchor:[20,0]});
       var markers = L.markerClusterGroup({spiderfyOnMaxZoom: false, showCoverageOnHover: true,zoomToBoundsOnClick: true});

        function loadLayer(url)
        {
                var myLayer = L.geoJson(url,{
                        onEachFeature:function onEachFeature(feature, layer) {
                                if (feature.properties && feature.properties.id) {
                                }

                        },
                        pointToLayer: function (feature, latlng) {
                        var marker = new L.Marker(latlng, { icon: ico });

                        markers[feature.properties.id] = marker;
                        marker.bindPopup('<img src="http://www.piersoft.it/panarotre/ajax-loader.gif">',{maxWidth:50, autoPan:true});

                      //  marker.on('click',showMarker());
                        return marker;
                        }
                });
                //.addTo(map);

                markers.addLayer(myLayer);
                map.addLayer(markers);
                markers.on('click',showMarker);
        }

microAjax('mappaf.json',function (res) {
var feat=JSON.parse(res);
loadLayer(feat);
  finishedLoading();
} );
function convertTimestamp(timestamp) {
  var d = new Date(timestamp * 1000),	// Convert the passed timestamp to milliseconds
		yyyy = d.getFullYear(),
		mm = ('0' + (d.getMonth() + 1)).slice(-2),	// Months are zero based. Add leading 0.
		dd = ('0' + d.getDate()).slice(-2),			// Add leading 0.
		hh = d.getHours(),
		h = hh,
		min = ('0' + d.getMinutes()).slice(-2),		// Add leading 0.
		ampm = 'AM',
		time;

	if (hh > 12) {
	//	h = hh - 12;
		ampm = 'PM';
	} else if (hh === 12) {
	//	h = 12;
		ampm = 'PM';
	} else if (hh == 0) {
		h = 12;
	}

	// ie: 2013-02-18, 8:35 AM
	time = h + ':' + min;

	return time;
}

 function showMarker(marker) {

   var jsonref=marker.layer.feature;
  //  console.log(jsonref);
  var i = 0;
 for (i=0;i<4;i++){
   microAjax('https://transit.land/api/v1/onestop_id/'+jsonref.properties.id, function (res) {

   var feat=JSON.parse(res);
   var index;
   console.log(feat.routes_serving_stop);
   console.log(jsonref.properties.id[0]);

//  alert (feat.length);
var text;



if(feat['routes_serving_stop'].length != "undefined")
{
  if(feat['routes_serving_stop'].length == "0")
  {
  text ="Non ci sono linee in arrivo nelle prossime ore";
  marker.layer.closePopup();
  marker.layer.bindPopup(text);
  marker.layer.openPopup();
  console.log("non ci sono linee");
}else{
  corse=feat['routes_serving_stop'].length;
urlj=jsonref.properties.id;
  text ="<b>"+jsonref.properties.name+"</b></br><a href='#' onClick='startLoading();MostrarVideo();finishedLoadinglong(corse);'>Clicca per orari</a></br>Linee: <b>";
console.log("Feat lenght: "+feat['routes_serving_stop'].length);

//for (i=0;i<feat['schedule_stop_pairs'].length;i++){
 for (i=0;i<feat['routes_serving_stop'].length;i++){

    //   // when the tiles load, remove the screen
    var last=feat['routes_serving_stop'][i];
console.log("routes_serving_stop:"+feat['routes_serving_stop'][i]['route_name']);
  //  var text ="Linee servite: "+last['IdLinea']+"<br>";
    text +="</br>"+last['route_name'];
//    var orario =last['route_name'];
//    text+="</b> orario<b>"+orario;
    marker.layer.closePopup();
    marker.layer.bindPopup(text);
    marker.layer.openPopup();
  }
}
}

}
  );
}
}

function startLoading() {
    loader.className = '';
}

function finishedLoadinglong(corse) {
    // first, toggle the class 'done', which makes the loading screen
    // fade out
    loader.className = 'done';
    if (corse >= 9){
    setTimeout(function() {

        loader.className = 'hide';
    }, 15000);
    }
    else if (corse >= 5){
    setTimeout(function() {

        loader.className = 'hide';
    }, 9000);
  }else if (corse >= 3){
    setTimeout(function() {

        loader.className = 'hide';
    }, 5000);
  }else{
    setTimeout(function() {

        loader.className = 'hide';
    }, 3000);
  }
}
function finishedLoading() {
    // first, toggle the class 'done', which makes the loading screen
    // fade out
    loader.className = 'done';
    setTimeout(function() {
        // then, after a half-second, add the class 'hide', which hides
        // it completely and ensures that the user can interact with the
        // map again.
        loader.className = 'hide';
    }, 500);
}
      sidebar.on('show', function () {
          console.log('Sidebar will be visible.');
      });

      sidebar.on('shown', function () {
          console.log('Sidebar is visible.');
      });

      sidebar.on('hide', function () {
          console.log('Sidebar will be hidden.');
      });

      sidebar.on('hidden', function () {
          console.log('Sidebar is hidden.');
      });

      L.DomEvent.on(sidebar.getCloseButton(), 'click', function () {
          console.log('Close button clicked.');
          location.reload();
      });
</script>

</body>
</html>

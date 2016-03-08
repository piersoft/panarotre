<? php
$lat=$_GET['lat'];
$lon=$_GET['lon'];
$r=$_GET['r'];
 ?>
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />
<title>Trasporti Pa-Na-Roma-Tr nelle vicinanze</title>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.mapbox.com/mapbox.js/v2.2.2/mapbox.js'></script>
<link href='https://api.mapbox.com/mapbox.js/v2.2.2/mapbox.css' rel='stylesheet' />
<meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=1.0, user-scalable=no">
<meta property="og:image" content="http://www.piersoft.it/panarotre/bus_.png"/>

</head>
<body onload="getLocation()">


<!--
  This example requires jQuery to load the file with AJAX.
  You can use another tool for AJAX.

  This pulls the file airports.csv, converts into into GeoJSON by autodetecting
  the latitude and longitude columns, and adds it to the map.

  Another CSV that you use will also need to contain latitude and longitude
  columns, and they must be similarly named.
-->

<script src='https://code.jquery.com/jquery-1.11.0.min.js'></script>
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-pip/v0.0.2/leaflet-pip.js'></script>
<script>
var latphp="";
var lonphp="";
var r="";

function getLocation() {

    if (navigator.geolocation ) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
alert('Abilita la localizzazione GPS per cortesia :) ')

    }
}
function showPosition(position) {
  //  x.innerHTML = "Latitude: " + position.coords.latitude +
  //  "<br>Longitude: " + position.coords.longitude;
  latphp = parseFloat('<?php printf($_GET['lat']); ?>');
  lonphp = parseFloat('<?php printf($_GET['lon']); ?>');
  r = parseFloat('<?php printf($_GET['r']); ?>');
  if (!latphp || 0 === latphp.length){
    latphp=position.coords.latitude;
    lonphp=position.coords.longitude;
    r=500;
  }else{

  }

console.log(latphp+" "+lonphp);
  window.location.href = "http://www.piersoft.it/napolitrasportibot/locator.php?lat="+latphp+"&lon="+lonphp+"&r="+r;
}
</script>
</body>
</html>

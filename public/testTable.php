<?php
session_start();
include ("../includes/header.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Interaktive Karte von Frankfurt</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>

<nav>
    <ul>
        <li><a href="?display=stations">Stationen</a></li>
        <li><a href="?display=users">Benutzer</a></li>
        <li><a href="?display=bikes">Fahrräder</a></li>
    </ul>
</nav>

<div id="map"></div>


<script>
    // Initialisierung der Karte und Setzen des Fokus auf Frankfurt
    var map = L.map('map').setView([50.1109, 8.6821], 13);

    // Hinzufügen der OpenStreetMap-Kacheln
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Kartendaten © <a href="https://www.openstreetmap.org/">OpenStreetMap</a>-Mitwirkende',
        maxZoom: 19,
    }).addTo(map);


    navigator.geolocation.watchPosition(success, error);
    let marker, circle, zoomed;

    function success(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const acc = position.coords.accuracy;

        // Falls bereits ein Marker/Kreis vorhanden ist, entfernen
        if (marker) {
            map.removeLayer(marker);
        }
        if (circle) {
            map.removeLayer(circle);
        }

        // Neuen Marker und Kreis hinzufügen
        marker = L.marker([lat, lon]).addTo(map);
        circle = L.circle([lat, lon], { radius: acc }).addTo(map);

        // Karte auf den neuen Standort zentrieren
        map.setView([lat, lon]);

    }


    function error(error) {

        if (error.code === 1) {
            alert('please allow geolocation access');
        } else{
            alert("cannot read location");
        }
    }


</script>

<?php include("../includes/footer.php")?>
</body>



</html>

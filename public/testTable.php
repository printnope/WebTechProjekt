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
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script
</head>

<body>
>
<nav>
    <ul>
        <li><a href="?display=stations">Stationen</a></li>
        <li><a href="?display=users">Benutzer</a></li>
        <li><a href="?display=bikes">Fahrräder</a></li>
    </ul>
</nav>

<div id="map"></div>





<?php include("../includes/footer.php")?>
</body>
<script>
    // Initialisierung der Karte und Setzen des Fokus auf Frankfurt
    var map = L.map('map').setView([50.1109, 8.6821], 13);

    // Hinzufügen der OpenStreetMap-Kacheln
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Kartendaten © <a href="https://www.openstreetmap.org/">OpenStreetMap</a>-Mitwirkende',
        maxZoom: 19,
    }).addTo(map);
</script>
</html>

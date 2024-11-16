<?php
function displayStations()
{


    $conn = include ("../databaseCon/db-connection.php");
    // Standartwerte aus der Tabelle stations auslesen
    $sql = "SELECT * FROM stations";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();


    echo '<div class="station-container">'; // Container für alle Stationen

    while ($row = $result->fetch_assoc()) {
        $stationID = $row["Station_ID"];
        echo '<div class="station-card">'; // Einzelner Stations-Container
        echo  '<a href="../public/testTable.php">';
        echo "<h2>Details für Station: " . htmlspecialchars($row['Station_Name']) . "</h2>";
        echo "<p>Station ID: " . $row['Station_ID'] . "</p>";
        echo "<p>Breitengrad: " . $row['Latitude'] . "</p>";
        echo "<p>Längengrad: " . $row['Longitude'] . "</p>";
        echo "<p>Startvorgänge: " . $row['Startvorgaenge'] . "</p>";
        echo "<p>Endvorgänge: " . $row['Endvorgaenge'] . "</p>";

        // Gesamtvorgänge berechnen
        $total_trips = $row['Startvorgaenge'];
        echo "<p>Gesamtvorgänge: " . $total_trips . "</p>";

        // Beliebtestes Ziel von dieser Station
        $sql_popular_destination = "SELECT Ende_Station, COUNT(*) AS count
                                    FROM routes
                                    WHERE Start_Station_ID = ?
                                    GROUP BY Ende_Station
                                    ORDER BY count DESC
                                    LIMIT 1";

        $stmt_popular = $conn->prepare($sql_popular_destination);
        $stmt_popular->bind_param("i", $stationID);
        $stmt_popular->execute();
        $result_popular = $stmt_popular->get_result();
        $popular_destination = $result_popular->fetch_assoc();
        echo "<p>Beliebtestes Ziel: " . ($popular_destination ? htmlspecialchars($popular_destination['Ende_Station']) : "N/A") . "</p>";

        // Stoßzeit an dieser Station
        $sql_peak_hour = "SELECT HOUR(Buchung_Start) AS peak_hour, COUNT(*) AS count
                          FROM routes
                          WHERE Start_Station_ID = ? OR Ende_Station_ID = ?
                          GROUP BY peak_hour
                          ORDER BY count DESC
                          LIMIT 1";

        $stmt_peak = $conn->prepare($sql_peak_hour);
        $stmt_peak->bind_param("ii", $stationID, $stationID);
        $stmt_peak->execute();
        $result_peak = $stmt_peak->get_result();
        $peak_hour = $result_peak->fetch_assoc();
        echo "<p>Stoßzeit: " . ($peak_hour ? $peak_hour['peak_hour'] . ":00 Uhr" : "N/A") . "</p>";
        echo '</a>';
        echo '</div>'; // Ende des Stations-Containers

        // Freigeben der Ergebnisse
        $stmt_popular->close();
        $stmt_peak->close();
    }
    echo '</div>'; // Ende des Containers für alle Stationen


    $stmt->close();
    $conn->close();


}



    function displayUser()
    {
        $conn = include("../databaseCon/db-connection.php");

        // Gesamtfahrten pro Nutzer ermitteln
        $sql = "SELECT DISTINCT Nutzer_ID FROM routes";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<div class="station-container">'; // Container für alle Nutzer

        while ($row = $result->fetch_assoc()) {
            $nutzerID = $row['Nutzer_ID'];
            echo '<div class="station-card">'; // Einzelner Nutzer-Container
            echo '<a href="../public/testTable.php">';
            echo "<h2>User ID: " . htmlspecialchars($nutzerID) . "</h2>";

            // Anzahl der Fahrten des Nutzers
            $sql_trips_count = "SELECT COUNT(*) AS total_trips
                            FROM routes
                            WHERE Nutzer_ID = ?";
            $stmt_trips_count = $conn->prepare($sql_trips_count);
            $stmt_trips_count->bind_param("s", $nutzerID);
            $stmt_trips_count->execute();
            $result_trips_count = $stmt_trips_count->get_result();
            $trips_count = $result_trips_count->fetch_assoc();
            echo "<p>Gesamtfahrten: " . (isset($trips_count['total_trips']) ? $trips_count['total_trips'] : "0") . "</p>";

            // Beliebteste Abholstation des Nutzers
            $sql_popular_start = "SELECT Start_Station, COUNT(*) AS count
                              FROM routes
                              WHERE Nutzer_ID = ?
                              GROUP BY Start_Station
                              ORDER BY count DESC
                              LIMIT 1";
            $stmt_popular_start = $conn->prepare($sql_popular_start);
            $stmt_popular_start->bind_param("s", $nutzerID);
            $stmt_popular_start->execute();
            $result_popular_start = $stmt_popular_start->get_result();
            $popular_start = $result_popular_start->fetch_assoc();
            echo "<p>Beliebteste Abholstation: " . (isset($popular_start['Start_Station']) ? htmlspecialchars($popular_start['Start_Station']) : "N/A") . "</p>";

            // Beliebteste Abgabestation des Nutzers
            $sql_popular_end = "SELECT Ende_Station, COUNT(*) AS count
                            FROM routes
                            WHERE Nutzer_ID = ?
                            GROUP BY Ende_Station
                            ORDER BY count DESC
                            LIMIT 1";
            $stmt_popular_end = $conn->prepare($sql_popular_end);
            $stmt_popular_end->bind_param("s", $nutzerID);
            $stmt_popular_end->execute();
            $result_popular_end = $stmt_popular_end->get_result();
            $popular_end = $result_popular_end->fetch_assoc();
            echo "<p>Beliebteste Abgabestation: " . (isset($popular_end['Ende_Station']) ? htmlspecialchars($popular_end['Ende_Station']) : "N/A") . "</p>";

            // Häufigster Wochentag der Fahrten des Nutzers
            $sql_frequent_day = "SELECT Wochentag, COUNT(*) AS count
                             FROM routes
                             WHERE Nutzer_ID = ?
                             GROUP BY Wochentag
                             ORDER BY count DESC
                             LIMIT 1";
            $stmt_frequent_day = $conn->prepare($sql_frequent_day);
            $stmt_frequent_day->bind_param("s", $nutzerID);
            $stmt_frequent_day->execute();
            $result_frequent_day = $stmt_frequent_day->get_result();
            $frequent_day = $result_frequent_day->fetch_assoc();
            echo "<p>Häufigster Wochentag: " . (isset($frequent_day['Wochentag']) ? htmlspecialchars($frequent_day['Wochentag']) : "N/A") . "</p>";

            echo '</div>'; // Ende des Nutzer-Containers

            // Freigeben der Ergebnisse
            $stmt_trips_count->close();
            $stmt_popular_start->close();
            $stmt_popular_end->close();
            $stmt_frequent_day->close();
        }
        echo '</a>';
        echo '</div>'; // Ende des Containers für alle Nutzer

        // Schließen der Datenbankverbindung
        $stmt->close();
        $conn->close();
}

function displayBikeDetails()
{
    $conn = include("../databaseCon/db-connection.php");

    // Alle Fahrrad-IDs abrufen
    $sql = "SELECT DISTINCT Fahrrad_ID FROM routes";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="station-container">'; // Container für alle Fahrräder

    while ($row = $result->fetch_assoc()) {
        $fahrradID = $row['Fahrrad_ID'];
        echo '<div class="station-card">'; // Einzelner Fahrrad-Container
        echo '<a href="../public/testTable.php">';
        echo "<h2>Fahrrad ID: " . htmlspecialchars($fahrradID) . "</h2>";

        // Beliebteste Startstation
        $sql_popular_start = "SELECT Start_Station, COUNT(*) AS count
                              FROM routes
                              WHERE Fahrrad_ID = ?
                              GROUP BY Start_Station
                              ORDER BY count DESC
                              LIMIT 1";
        $stmt_popular_start = $conn->prepare($sql_popular_start);
        $stmt_popular_start->bind_param("i", $fahrradID);
        $stmt_popular_start->execute();
        $result_popular_start = $stmt_popular_start->get_result();
        $popular_start = $result_popular_start->fetch_assoc();
        echo "<p>Beliebteste Startstation: " . (isset($popular_start['Start_Station']) ? htmlspecialchars($popular_start['Start_Station']) : "N/A") . "</p>";

        // Beliebteste Endstation
        $sql_popular_end = "SELECT Ende_Station, COUNT(*) AS count
                            FROM routes
                            WHERE Fahrrad_ID = ?
                            GROUP BY Ende_Station
                            ORDER BY count DESC
                            LIMIT 1";
        $stmt_popular_end = $conn->prepare($sql_popular_end);
        $stmt_popular_end->bind_param("i", $fahrradID);
        $stmt_popular_end->execute();
        $result_popular_end = $stmt_popular_end->get_result();
        $popular_end = $result_popular_end->fetch_assoc();
        echo "<p>Beliebteste Endstation: " . (isset($popular_end['Ende_Station']) ? htmlspecialchars($popular_end['Ende_Station']) : "N/A") . "</p>";

        // Häufigster Nutzer des Fahrrads
        $sql_frequent_user = "SELECT Nutzer_ID, COUNT(*) AS count
                              FROM routes
                              WHERE Fahrrad_ID = ?
                              GROUP BY Nutzer_ID
                              ORDER BY count DESC
                              LIMIT 1";
        $stmt_frequent_user = $conn->prepare($sql_frequent_user);
        $stmt_frequent_user->bind_param("i", $fahrradID);
        $stmt_frequent_user->execute();
        $result_frequent_user = $stmt_frequent_user->get_result();
        $frequent_user = $result_frequent_user->fetch_assoc();
        echo "<p>Häufigster Nutzer: " . (isset($frequent_user['Nutzer_ID']) ? htmlspecialchars($frequent_user['Nutzer_ID']) : "N/A") . "</p>";

        // Nutzung pro Wochentag
        $sql_weekday_usage = "SELECT Wochentag, COUNT(*) AS count
                              FROM routes
                              WHERE Fahrrad_ID = ?
                              GROUP BY Wochentag
                              ORDER BY FIELD(Wochentag, 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So')";
        $stmt_weekday_usage = $conn->prepare($sql_weekday_usage);
        $stmt_weekday_usage->bind_param("i", $fahrradID);
        $stmt_weekday_usage->execute();
        $result_weekday_usage = $stmt_weekday_usage->get_result();

        echo "<p>Nutzung pro Wochentag:</p>";
        echo "<ul>";
        while ($weekday_row = $result_weekday_usage->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($weekday_row['Wochentag']) . ": " . $weekday_row['count'] . " Fahrten</li>";
        }
        echo "</ul>";
        echo '</a>';
        echo '</div>'; // Ende des Fahrrad-Containers

        // Freigeben der Ergebnisse
        $stmt_popular_start->close();
        $stmt_popular_end->close();
        $stmt_frequent_user->close();
        $stmt_weekday_usage->close();
    }

    echo '</div>'; // Ende des Containers für alle Fahrräder

    $stmt->close();
    $conn->close();
}

?>

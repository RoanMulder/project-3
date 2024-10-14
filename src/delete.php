<?php

$methodType = $_SERVER['REQUEST_METHOD']; // Haal de methode van het HTTP-verzoek op

if ($methodType == 'GET' && isset($_GET['id'])) { // Controleer of de methode GET is en of de parameter 'idHorloge' is ingesteld
    try {
        $host = "localhost"; // Hostnaam voor databaseverbinding
        $username = "root"; // Gebruikersnaam voor databaseverbinding
        $password = "root"; // Wachtwoord voor databaseverbinding
        $database = "gegevensverzameling"; // Naam van de database

        $Id = $_GET['id']; // Haal de waarde van de parameter 'idHorloge' op

        $connection = new mysqli($host, $username, $password, $database); // Maak een nieuwe MySQLi-verbinding

        if ($connection->connect_error) { // Controleer op fouten bij het maken van de verbinding
            throw new Exception($connection->connect_error); // Gooi een uitzondering als er een fout optreedt bij het maken van de verbinding
        }

        $query = "DELETE FROM users WHERE id = ?"; // SQL-query om een horloge te verwijderen

        $statement = $connection->prepare($query); // Bereid de SQL-query voor
        $statement->bind_param("i", $Id); // Koppel de parameter aan de voorbereide statement

        if (!$statement->execute()) { // Voer de voorbereide statement uit en controleer op fouten
            throw new Exception($statement->error); // Gooi een uitzondering als het uitvoeren van de statement mislukt
        }

        header("Location: overzicht.php"); // Stuur de gebruiker door naar de lijst met horloges
        exit(); // Stop de scriptuitvoering na het omleiden
    } catch (Exception $e) { // Vang eventuele uitzonderingen op
        echo "Er is een fout opgetreden: " . $e->getMessage(); // Toon een foutmelding als er een uitzondering optreedt
    } finally { // Voer deze code altijd uit, zelfs als er een uitzondering wordt opgevangen
        if ($statement) { // Controleer of de statement is ingesteld
            $statement->close(); // Sluit de voorbereide statement
        }
        if ($connection) { // Controleer of de databaseverbinding is ingesteld
            $connection->close(); // Sluit de databaseverbinding
        }
    }
}
?>

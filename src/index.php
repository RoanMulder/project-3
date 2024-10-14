<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$email = $telefoonnummer = $klacht = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $host = "localhost";
        $gebruiker = "root";
        $password = "root";
        $database = "gegevensverzameling";

        // Collect and validate POST data
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $telefoonnummer = preg_replace('/[^0-9]/', '', $_POST['telefoonnummer']); // Strips non-numeric chars
        $klacht = trim($_POST['klacht']);

        // Check if fields are not empty
        if (!$email || empty($telefoonnummer) || empty($klacht)) {
            throw new Exception("Alle velden moeten worden ingevuld.");
        }

        // Create database connection
        $connectie = new mysqli($host, $gebruiker, $password, $database);

        if ($connectie->connect_error) {
            throw new Exception($connectie->connect_error);
        }

        // Prepare and execute the SQL statement
        $query = "INSERT INTO users (email, telefoonnummer, klacht) VALUES (?, ?, ?)";
        $statement = $connectie->prepare($query);
        $statement->bind_param("sis", $email, $telefoonnummer, $klacht);

        if ($statement->execute()) {
            // Redirect to the same page with a success parameter
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } else {
            throw new Exception("Databasefout: " . $statement->error);
        }
    } catch (Exception $e) {
        echo "Oepsie: " . htmlspecialchars($e->getMessage());
    } finally {
        if (isset($statement)) {
            $statement->close();
        }
        if (isset($connectie)) {
            $connectie->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="../css/basic.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/popup.css">
</head>
<body>

<header>
    <h2 id="title">MetaTech</h2>

    <div class="logo-container">
        <img src="../images/Logo.svg" alt="MetaTech Logo">
    </div>

    <nav class="nav-menu" id="nav-menu">
        <div class="nav-item"><a href="index.php">Home</a></div>
        <div class="nav-item"><a href="overzicht.php">Administratie</a></div>
    </nav>
</header>

<main>
    <div class="container">
        <h1>Persoonlijke gegevens</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Vul je e-mail in" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="tel" id="telefoonnummer" name="telefoonnummer" placeholder="Vul je telefoonnummer in" value="<?php echo htmlspecialchars($telefoonnummer); ?>" required>

            <label for="klacht">Klacht:</label>
            <input type="text" id="klacht" name="klacht" placeholder="Vul je klacht in" value="<?php echo htmlspecialchars($klacht); ?>" required>

            <input id="button" type="submit" value="Toevoegen">
        </form>
    </div>
</main>

<footer>
    <!-- Footer content -->
</footer>

<script src="../js/javascript.js"></script>
<script src="../js/popup.js"></script>
</body>
</html>
<!--
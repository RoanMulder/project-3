<?php 
$methodType = $_SERVER['REQUEST_METHOD']; // Get the HTTP request type (GET or POST)

// Check if the request type is GET
if ($methodType == 'GET') {
    if (isset($_GET['id'])) { // Check if the parameter 'id' is set in the GET request
        try {
            // Database connection details
            $host = "localhost";
            $user = "root";
            $pass = "root";
            $db = "gegevensverzameling"; // Database name

            $userId = $_GET['id']; // Get the 'id' from the GET request

            $connection = new mysqli($host, $user, $pass, $db); // Create a database connection
            if ($connection->connect_error) {
                throw new Exception($connection->connect_error); // Check for connection errors
            }

            $query = "SELECT id, email, telefoonnummer, klacht FROM users WHERE id = ?"; // SQL query to retrieve data
            $statement = $connection->prepare($query); // Prepare the query
            $statement->bind_param("i", $userId); // Bind the 'id' parameter to the query

            if (!$statement->execute()) { 
                throw new Exception($statement->error); // Execute the query and check for errors
            }

            $statement->bind_result($id, $email, $telefoonnummer, $klacht); // Bind the results to variables
            $statement->fetch(); // Fetch the results

            $userEmail = htmlspecialchars($email); // Sanitize the retrieved 'email'
            $userPhone = htmlspecialchars($telefoonnummer); // Sanitize the retrieved 'telefoonnummer'
            $userKlacht = htmlspecialchars($klacht); // Sanitize the retrieved 'klacht'
        } catch (Exception $e) {
            echo "Something went wrong: " . $e->getMessage(); // Handle exceptions by displaying an error message
        } finally {
            if (isset($statement)) {
                $statement->close(); // Close the statement
            }
            if (isset($connection)) {
                $connection->close(); // Close the database connection
            }
        }
    }
} else if ($methodType == 'POST') { // Check if the request type is POST
    if (isset($_POST['id'])) { // Check if the 'id' parameter is set in the POST request
        try {
            // Database connection details
            $host = "localhost";
            $user = "root";
            $pass = "root";
            $db = "gegevensverzameling"; // Database name

            $postId = htmlspecialchars($_POST['id']); // Sanitize the 'id' from POST request

            $postEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if (!filter_var($postEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            $postPhone = htmlspecialchars($_POST['telefoonnummer']); // Sanitize the 'telefoonnummer' from POST request

            $userKlacht = htmlspecialchars($_POST['klacht']); // Assume the user is a klacht 1 for simplicity (replace with actual user classification)

            $connection = new mysqli($host, $user, $pass, $db); // Create a database connection
            if ($connection->connect_error) {
                throw new Exception($connection->connect_error); // Check for connection errors
            }

            $query = "UPDATE users SET email = ?, telefoonnummer = ?, klacht = ? WHERE id = ?";
            $statement = $connection->prepare($query); // Prepare the query
            $statement->bind_param("siss", $postEmail, $postPhone, $userKlacht, $postId); // Bind the parameters to the query

            if (!$statement->execute()) {
                throw new Exception($statement->error); // Execute the query and check for errors
            }

            header("Location: overzicht.php"); // Redirect to gegevensLijst.php after successful update
            exit(); // Exit to ensure no further code is executed

        } catch (Exception $e) {
            echo "Something went wrong: " . $e->getMessage(); // Handle exceptions by displaying an error message
        } finally {
            if (isset($statement)) {
                $statement->close(); // Close the statement
            }
            if (isset($connection)) {
                $connection->close(); // Close the database connection
            }
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
    <link rel="stylesheet" href="../css/update.css">
</head>
<body>

<main>
    <div class="container">
        <h1>Persoonlijke gegevens</h1>
        <form action="update.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($userId) ?>">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($userEmail) ?>" required>
            <label>Telefoonnummer:</label>
            <input type="text" name="telefoonnummer" value="<?php echo htmlspecialchars($userPhone) ?>" required>
            <label>Klacht:</label>
            <input type="text" name="klacht" value="<?php echo htmlspecialchars($userKlacht) ?>" required>
            <input type="submit" name="Update" value="Update" >            
            <a href="overzicht.php" class="return-btn">Terug naar overzicht</a>
        </form>
    </div>
</main>

</body>
</html>

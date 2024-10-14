<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Overzicht</title>
    <link rel="stylesheet" href="../css/basic.css">
    <link rel="stylesheet" href="../css/overzicht.css">
    </head>
<body>

<header>
    <h2 id="title">MetaTech</h2>

    <nav class="nav-menu" id="nav-menu">
        <div class="nav-item"><a href="index.php">Home</a></div>
        <div class="nav-item"><a href="overzicht.php">Administratie</a></div>
    </nav>
</header>

<main>

    <div class="container">
        <h1>Overzicht van gegevens</h1>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        try {
            // Database connection parameters
            $host = "localhost";
            $user = "root";
            $pass = "root";
            $db = "gegevensverzameling";

            // Connect to the database
            $connection = new mysqli($host, $user, $pass, $db);

            // Check connection
            if ($connection->connect_error) {
                throw new Exception($connection->connect_error);
            }

            // Backup logic
            if (isset($_POST['backup'])) {
                $backupDir = 'backups/';
                
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0777, true);  // Create the directory if it doesn't exist
                }
            
                // Name for the backup file (with timestamp)
                $backupFile = $backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            
                // Escape shell arguments for security, use full path for mysqldump
                $command = "\"C:\\MAMP\\bin\\mysql\\bin\\mysqldump.exe\" --user=$user --password=$pass --host=$host $db > \"$backupFile\" 2>&1";
                
                exec($command, $output, $result);
            
                if ($result === 0) {
                    echo "<p>Backup successful. <a href='" . htmlspecialchars($backupFile) . "'>Download backup</a></p>";
                } else {
                    echo "<p>Backup failed. Error: " . implode("\n", $output) . "</p>";
                }

                // Pass the command and result to JavaScript
                $commandLog = "<pre>$command</pre><pre>Result: $result</pre><pre>Output: " . implode("\n", $output) . "</pre>";
                echo "<script>
                        document.getElementById('log-content').innerHTML = " . json_encode($commandLog) . ";
                        document.getElementById('myModal').style.display = 'block';
                      </script>";
            }

            // Query to select data
            $query = "SELECT id, email, telefoonnummer, klacht FROM users";
            $statement = $connection->prepare($query);

            // Execute the query
            if (!$statement->execute()) {
                throw new Exception($statement->error);
            }

            // Bind results
            $statement->bind_result($id, $email, $telefoonnummer, $klacht);

            // Output the table
            echo "<table>
                    <tr>
                        <th>Email</th>
                        <th>Telefoonnummer</th>
                        <th>Klacht</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>";

            // Fetch and display results
            while ($statement->fetch()) {
                echo "<tr>
                        <td>" . htmlspecialchars($email) . "</td>
                        <td>" . htmlspecialchars($telefoonnummer) . "</td>
                        <td>" . htmlspecialchars($klacht) . "</td>
                        <td><a href='update.php?id=" . htmlspecialchars($id) . "'>Update</a></td>
                        <td><a href='delete.php?id=" . htmlspecialchars($id) . "'>Delete</a></td>
                    </tr>";
            }
            echo "</table>";
        } catch (Exception $e) {
            // Display any errors
            echo "<p>Er is iets misgegaan: " . htmlspecialchars($e->getMessage()) . "</p>";
        } finally {
            // Close the statement and connection
            if (isset($statement)) {
                $statement->close();
            }
            if (isset($connection)) {
                $connection->close();
            }
        }
        ?>

        <!-- Backup Button Form -->
        <form action="" method="post">
            <button class="backup" type="submit" name="backup" value="backup">Backup</button>
        </form>
        
        

        <div class="pdf">
            <a href="export_pdf.php" target="_blank">Export to PDF</a>
        </div>


        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <div id="log-content"></div>
            </div>
        </div>

    </div>

    <img src="../images/Logo.svg" alt="MetaTech Logo">

</main>

<footer>
    <!-- Add footer content if needed -->
</footer>

<script>
    // Modal control
    var modal = document.getElementById('myModal');
    var closeModal = document.getElementById('closeModal');

    // Close the modal when the user clicks on <span> (x)
    closeModal.onclick = function() {
        modal.style.display = 'none';
    }

    // Close the modal when the user clicks anywhere outside of the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
</body>
</html>

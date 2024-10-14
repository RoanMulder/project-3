<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;

// Instantiate Dompdf
$dompdf = new Dompdf();

// Build HTML content for the PDF (adjust this as needed)
$html = '<h1>Gegevens Overzicht</h1>';
$html .= '<table border="1">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Telefoonnummer</th>
                    <th>Klacht</th>
                </tr>
            </thead>
            <tbody>';

// Fetch data from database (reuse your existing code)
$host = "localhost";
$user = "root";
$pass = "root";
$db = "gegevensverzameling";
$connection = new mysqli($host, $user, $pass, $db);
$query = "SELECT email, telefoonnummer, klacht FROM users";
$statement = $connection->prepare($query);

if ($statement->execute()) {
    $statement->bind_result($email, $telefoonnummer, $klacht);
    while ($statement->fetch()) {
        $html .= "<tr>
                    <td>" . htmlspecialchars($email) . "</td>
                    <td>" . htmlspecialchars($telefoonnummer) . "</td>
                    <td>" . htmlspecialchars($klacht) . "</td>
                  </tr>";
    }
}

$html .= '</tbody></table>';

// Load HTML into Dompdf
$dompdf->loadHtml($html);

// Set paper size
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output the PDF (you can also force download by setting Attachment => true)
$dompdf->stream("gegevens_overzicht.pdf", ["Attachment" => false]);
?>

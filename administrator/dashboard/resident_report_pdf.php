<?php
session_start();
include_once('../../config/config.php');
// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}

// Fix: Add missing slash before 'vendor/autoload.php'
require_once __DIR__ . '/../../vendor/autoload.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('WMVMS');
$pdf->SetTitle('Resident Report');
$pdf->SetHeaderData('', 0, 'Resident Report', 'Generated Report');

// Set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Fetch data grouped by month
$query = "SELECT `id`, `violationID`, `resident_name`, 'tag_name', `description`, `latitude`, 
                 `longitude`, `date`, `time`, `admin_approval`, `isActive` 
          FROM `tbl_resident_report`
          ORDER BY `date` ASC";

$result = $conn->query($query);

// Check if results exist
if ($result->num_rows > 0) {
    $currentMonth = null;
    $html = '';

    while ($row = $result->fetch_assoc()) {
        $reportDate = new DateTime($row['date']);
        $monthYear = $reportDate->format('F Y'); // Format: November 2024

        // Add a new section for each month
        if ($currentMonth !== $monthYear) {
            if ($currentMonth !== null) {
                $html .= '</tbody></table><br>'; // Close previous table
            }

            $currentMonth = $monthYear;
            $html .= "<h2 style='text-align: center;'>Reports for $currentMonth</h2>";
            $html .= '
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>Resident Name</th>
                        <th>Tag Name</th>
                        <th>Description</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Admin Approval</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>';
        }

        // Add row data
        $html .= '
        <tr>
            <td>' . htmlspecialchars($row['resident_name']) . '</td>
            <td>' . htmlspecialchars($row['tag_name']) . '</td>
            <td>' . htmlspecialchars($row['description']) . '</td>
            <td>' . htmlspecialchars($row['latitude']) . '</td>
            <td>' . htmlspecialchars($row['longitude']) . '</td>
            <td>' . htmlspecialchars($row['date']) . '</td>
            <td>' . htmlspecialchars($row['time']) . '</td>
            <td>' . ($row['admin_approval'] ? 'Yes' : 'No') . '</td>
            <td>' . ($row['isActive'] ? 'Yes' : 'No') . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';
} else {
    $html = '<p>No reports found.</p>';
}

// Output the HTML content as PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document (D = Download)
$pdf->Output('resident_report.pdf', 'D');

// Close the database connection
$conn->close();
?>

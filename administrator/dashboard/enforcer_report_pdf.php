<?php
session_start();

// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

require_once __DIR__ . '/../../vendor/autoload.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wmvms";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('WMVMS');
$pdf->SetTitle('Enforcer Report');
$pdf->SetHeaderData('', 0, 'Enforcer Report', 'Generated Report');

// Set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Fetch data from the database grouped by month
$query = "SELECT `id`, `violationID`, `resident_name`, `violators_name`, `violators_age`, 
                 `violators_gender`, `violators_location`, `violation_type`, `datetime`, 
                 `latitude`, `longitude`, `penalty`, 
                 DATE_FORMAT(`datetime`, '%M %Y') AS `report_month` 
          FROM `tbl_enforcer_report` 
          ORDER BY `datetime` ASC";

$result = $conn->query($query);

// Initialize HTML content
$html = '<h2 style="text-align: center;">Enforcer Reports</h2>';

if ($result->num_rows > 0) {
    $currentMonth = '';
    
    // Process data row by row
    while ($row = $result->fetch_assoc()) {
        $month = $row['report_month'];
        
        // Add a new table for a new month
        if ($month !== $currentMonth) {
            if (!empty($currentMonth)) {
                $html .= '</tbody></table><br><br>'; // Close previous table
            }
            
            $html .= '<h3 style="text-align: center;">' . htmlspecialchars($month) . '</h3>';
            $html .= '
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>Resident Name</th>
                        <th>Violator Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Penalty</th>
                    </tr>
                </thead>
                <tbody>';
            $currentMonth = $month;
        }
        
        // Add a row for the current record
        $html .= '
        <tr>
            <td>' . htmlspecialchars($row['resident_name']) . '</td>
            <td>' . htmlspecialchars($row['violators_name']) . '</td>
            <td>' . htmlspecialchars($row['violators_age']) . '</td>
            <td>' . htmlspecialchars($row['violators_gender']) . '</td>
            <td>' . htmlspecialchars($row['violators_location']) . '</td>
            <td>' . htmlspecialchars($row['violation_type']) . '</td>
            <td>' . htmlspecialchars($row['datetime']) . '</td>
            <td>' . htmlspecialchars($row['penalty']) . '</td>
        </tr>';
    }
    
    // Close the last table
    $html .= '</tbody></table>';
} else {
    $html .= '<p>No reports found.</p>';
}

// Output the HTML content as PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document (D = Download)
$pdf->Output('enforcer_report.pdf', 'D');

// Close the database connection
$conn->close();
?>

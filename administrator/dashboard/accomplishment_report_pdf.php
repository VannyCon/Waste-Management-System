<?php
session_start();
include_once('../../config/config.php');

// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
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

// Retrieve the selected year and month from the query parameters
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : null;
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : null;

// Validate year and month
if ($selectedYear === null || $selectedMonth === null || $selectedMonth < 1 || $selectedMonth > 12) {
    die('Error: Year and Month must be provided and valid.');
}

// ------------------- First Query and Table -------------------

// Query to fetch data from `dashboard` table
$query = "SELECT `penalty_year`, `penalty_month`, `total_penalties`, `total_penalty_paid`, `total_penalty_unpaid`, `total_unsolved`, `total_solved`, `total_report`
          FROM `dashboard`
          WHERE `penalty_year` = $selectedYear AND `penalty_month` = $selectedMonth
          ORDER BY `penalty_year` DESC, `penalty_month` DESC";

$result = $conn->query($query);

// Check if results exist
$html = '';
if ($result->num_rows > 0) {
    $currentMonth = null;
    $html .= '<h1 class="text-center">Accomplishment Reports</h1>';

    while ($row = $result->fetch_assoc()) {
        // Get full month name and year
        $monthYear = DateTime::createFromFormat('!m', $row['penalty_month']);
        $monthName = $monthYear->format('F');  // Full month name
        $year = $row['penalty_year'];

        // Add a new section for each month
        if ($currentMonth !== $monthName . ' ' . $year) {
            if ($currentMonth !== null) {
                $html .= '</tbody></table><br>'; // Close previous table
            }

            $currentMonth = $monthName . ' ' . $year;
            $html .= "<h2 style='text-align: center;'>Reports for $currentMonth</h2>";
            $html .= '
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="text-align: center;">Total Penalties</th>
                        <th style="text-align: center;">Total Paid Penalties</th>
                        <th style="text-align: center;">Total Unpaid Penalties</th>
                        <th style="text-align: center;">Total Solved</th>
                        <th style="text-align: center;">Total Unsolved</th>
                        <th style="text-align: center;">Total Reports</th>
                    </tr>
                </thead>
                <tbody>';
        }

        // Add row data for the current month
        $html .= '
        <tr>
            <td style="text-align: right;">' . number_format($row['total_penalties'], 2) . '</td>
            <td style="text-align: right;">' . number_format($row['total_penalty_paid'], 2) . '</td>
            <td style="text-align: right;">' . number_format($row['total_penalty_unpaid'], 2) . '</td>
            <td style="text-align: center;">' . $row['total_solved'] . '</td>
            <td style="text-align: center;">' . $row['total_unsolved'] . '</td>
            <td style="text-align: center;">' . $row['total_report'] . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';
} else {
    $html .= '<p>No reports found for the selected year and month.</p>';
}

// Write the first table to the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// ------------------- Second Query and Table -------------------

// Query to fetch data from `tbl_enforcer_report` table
$query2 = "SELECT `id`, `violationID`, `resident_name`, `violators_name`, `violators_age`, `violators_gender`, `violators_location`, `violation_type`, `datetime`, `latitude`, `longitude`, `penalty`, `isPaid` FROM `tbl_enforcer_report` WHERE YEAR(`datetime`) = $selectedYear AND MONTH(`datetime`) = $selectedMonth";

$result2 = $conn->query($query2);

// Check if results exist
$html2 = '';
if ($result2->num_rows > 0) {
    $html2 .= '<h1 class="text-center"> Reports</h1>';
    $html2 .= '
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="text-align: center;">Resident Name</th>
                <th style="text-align: center;">Violator Name</th>
                <th style="text-align: center;">Age</th>
                <th style="text-align: center;">Gender</th>
                <th style="text-align: center;">Location</th>
                <th style="text-align: center;">Violation Type</th>
                <th style="text-align: center;">Offenses</th>
                <th style="text-align: center;">Penalty</th>
                <th style="text-align: center;">Paid</th>
            </tr>
        </thead>
        <tbody>';
    
    while ($row2 = $result2->fetch_assoc()) {
        $html2 .= '
        <tr>
            <td style="text-align: center;">' . $row2['resident_name'] . '</td>
            <td style="text-align: center;">' . $row2['violators_name'] . '</td>
            <td style="text-align: center;">' . $row2['violators_age'] . '</td>
            <td style="text-align: center;">' . $row2['violators_gender'] . '</td>
            <td style="text-align: center;">' . $row2['violators_location'] . '</td>
            <td style="text-align: center;">' . $row2['violation_type'] . '</td>
            <td style="text-align: center;">';
        
        // Check the penalty and set the appropriate badge
        if ($row2['penalty'] == '500') {
            $html2 .= '<span class="badge badge-success">First Offense</span>';
        } else if ($row2['penalty'] == '1000') {
            $html2 .= '<span class="badge badge-warning">Second Offense</span>';
        } else if ($row2['penalty'] == '5000') {
            $html2 .= '<span class="badge badge-danger">Third Offense</span>';
        }
        
        $html2 .= '
            </td>
            <td style="text-align: right;">' . number_format($row2['penalty'], 2) . '</td>
            <td style="text-align: center; color: ' . ($row2['isPaid'] ? 'green' : 'red') . ';">' . ($row2['isPaid'] ? 'Yes' : 'No') . '</td>

        </tr>';
    }

    $html2 .= '</tbody></table>';
} else {
    $html2 .= '<p>No enforcer reports found for the selected year and month.</p>';
}


// Write the second table to the PDF
$pdf->writeHTML($html2, true, false, true, false, '');

// Close and output PDF document (D = Download)
$pdf->Output('resident_report.pdf', 'D');

// Close the database connection
$conn->close();
?>

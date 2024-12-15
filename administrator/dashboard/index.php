<?php 
session_start();
include('../../config/config.php');
// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}


$title = "dashboard";
require_once("templates/headers.php");
require_once("templates/nav.php");


// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
#
//////////////////////////////////////////////////////////////////
// Fetch summary data
$sql0 = "SELECT `total_penalties`, `total_penalty_paid`, `total_penalty_unpaid`, `total_unsolved`, `total_solved`, `total_report` FROM `penalty_summary` WHERE 1";
$dashboard = $conn->query($sql0);
$dashboard_data = $dashboard->fetch_assoc();

$sql001 = "SELECT `total_penalties`, `total_penalty_paid`, `total_penalty_unpaid`, `total_unsolved`, `total_solved`, `total_report` FROM `penalty_summary` WHERE 1";
$dashboard_totalrep = $conn->query($sql001);
$dashboard_totalreps = $dashboard_totalrep->fetch_assoc();

// Variables for card values
$totalReports = $dashboard_data['total_report'] ?? 0;

$total_penalties = isset($dashboard_data['total_penalties']) ? '₱' . number_format($dashboard_data['total_penalties'], 2) : '₱0.00';
$total_penalty_paid = isset($dashboard_data['total_penalty_paid']) ? '₱' . number_format($dashboard_data['total_penalty_paid'], 2) : '₱0.00';
$total_penalty_unpaid = isset($dashboard_data['total_penalty_unpaid']) ? '₱' . number_format($dashboard_data['total_penalty_unpaid'], 2) : '₱0.00';

$total_solved = $dashboard_data['total_solved'] ?? 0;
$total_unsolved = $dashboard_data['total_unsolved'] ?? 0;
//////////////////////////////////////////////////////////////////////





// Fetch summary data
$sql = "SELECT total_report, total_active_violation, total_count_violation, total_citizen_count FROM summary_report_view";
$result = $conn->query($sql);
$summary = $result->fetch_assoc();

// Variables for card values
$totalReport = $summary['total_report'] ?? 0;
$totalActiveViolation = $summary['total_active_violation'] ?? 0;
$totalCountViolation = $summary['total_count_violation'] ?? 0;
$totalCitizenCount = $summary['total_citizen_count'] ?? 0;

// Prepare all months for the graph
$allMonths = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

$year = date('Y'); // Use the current year or change as needed
$monthlyViolations = array_fill_keys($allMonths, 0); // Initialize all months with 0


// Fetch violation data
$sql1 = "SELECT month_name, year, violation_count 
         FROM monthly_violation_count 
         WHERE year = $year 
         ORDER BY FIELD(month_name, 
         'January', 'February', 'March', 'April', 'May', 'June', 
         'July', 'August', 'September', 'October', 'November', 'December')";
$result1 = $conn->query($sql1);

// Populate the data for months with violations
while ($row = $result1->fetch_assoc()) {
    $month = $row['month_name'];
    $monthlyViolations[$month] = (int)$row['violation_count'];
}

// Prepare chart data
$months = [];
$violations = [];

foreach ($monthlyViolations as $month => $count) {
    $months[] = "$month";  // Example: "October 2024"
    $violations[] = $count;
}
?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Load Chart.js -->
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
    <div class="container-fluid">
        <div class="mx-0">
        </div>

        <div class="row">
            <!-- Total Report Card -->
            <div class="col-lg-3 col-sm-6">
                <a href="total_reports.php" style="text-decoration: none; color: inherit;">
                    <div class="card">
                        <div class="stat-widget-one card-body">
                            <div class="stat-icon d-inline-block">
                                <i class="ti-file text-danger border-danger"></i>
                            </div>
                            <div class="stat-content d-inline-block">
                                <div class="stat-text"><b>Total Report</b></div>
                                <div class="stat-digit"><?php echo $totalReports; ?></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <!-- Active Violations Card -->
            <div class="col-lg-3 col-sm-6">
                <a href="paided_violation.php" style="text-decoration: none; color: inherit;">
                    <div class="card">
                        <div class="stat-widget-one card-body">
                            <div class="stat-icon d-inline-block">
                                <i class="ti-check-box text-success border-success"></i>
                            </div>
                            <div class="stat-content d-inline-block">
                                <div class="stat-text"><b>Solved Reports</b></div>
                                <div class="stat-digit"><?php echo $total_solved; ?></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Violations Card -->
            <div class="col-lg-3 col-sm-6">
                <a href="enforcer_report.php" style="text-decoration: none; color: inherit;">
                    <div class="card">
                        <div class="stat-widget-one card-body">
                            <div class="stat-icon d-inline-block">
                                <i class="ti-alert text-warning border-warning"></i>
                            </div>
                            <div class="stat-content d-inline-block">
                                <div class="stat-text"><b>Unsolve Reports</b></div>
                                <div class="stat-digit"><?php echo $total_unsolved; ?></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Concerned Citizens Card -->
            <div class="col-lg-3 col-sm-6">
                <div class="card" id="penaltyCard">
                    <div class="stat-widget-one card-body">
                        <div class="stat-icon d-inline-block">
                            <i class="ti-comments text-primary border-primary"></i>
                        </div>
                        <div class="stat-content d-inline-block">
                            <div class="stat-text"><b>Total Penalties</b></div>
                            <div class="stat-digit"><?php echo $total_penalties; ?></div>
                        </div>
                    </div>
                </div>
            </div>


        <div class="card p-4 col-12">
        <h2>Monthly Violation Count</h2>
        <canvas id="violationChart" height="400"></canvas> <!-- Chart container -->
    </div>


    <!-- Modal -->
<div class="modal fade" id="penaltyModal" tabindex="-1" aria-labelledby="penaltyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="penaltyModalLabel">Penalty Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark">
                <p><strong>Total Unpaid Penalties: </strong><span class="text-danger" id="unpaidPenalties"></span><a href="total_unpaid.php" class="btn btn-info mx-2">Check</a></p>
                <p><strong>Total Paid Penalties: </strong><span class="text-success"  id="paidPenalties"></span><a href="total_paid.php" class="btn btn-info mx-2">Check</a></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Prepare data for Chart.js
    const labels = <?php echo json_encode($months); ?>;
    const data = <?php echo json_encode($violations); ?>;

    const ctx = document.getElementById('violationChart').getContext('2d');
    const violationChart = new Chart(ctx, {
        type: 'bar', // Use bar chart
        data: {
            labels: labels,
            datasets: [{
                label: 'Violations Count',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.5)', // Bar color
                borderColor: 'rgba(75, 192, 192, 1)', // Border color
                borderWidth: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { enabled: true }
            },
            scales: {
                y: {
                    beginAtZero: true, // Y-axis starts at 0
                    max: 100 // Set y-axis maximum to 100
                }
            }
        }
    });
</script>
<!-- jQuery and Bootstrap JS (ensure you're loading Bootstrap and jQuery for this to work) -->
<script>
    $(document).ready(function() {
        // Click event to open modal
        $('#penaltyCard').on('click', function() {
            // Fetch data for unpaid and paid penalties (you can get this from PHP variables or AJAX)
            var unpaidPenalties = '<?php echo $total_penalty_unpaid; ?>';  // Replace with actual value from PHP
            var paidPenalties = '<?php echo $total_penalty_paid; ?>';  // Replace with actual value from PHP

            // Populate the modal with data
            $('#unpaidPenalties').text(unpaidPenalties);
            $('#paidPenalties').text(paidPenalties);

            // Show the modal
            $('#penaltyModal').modal('show');
        });
    });
</script>
<?php
   require_once("templates/footer.php");
?>

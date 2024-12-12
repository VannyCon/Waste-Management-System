<?php
session_start();

// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}

$title = "report";
require_once("templates/headers.php");
require_once("templates/nav.php");
?>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


<style>
    /* Center the card vertically and horizontally */
    .center-card {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="center-card">
            <div class="text-center w-50 py-3 px-2" >
                <div class="row">
                    <div class="col card mx-2  py-2">
                        <p><strong>Enforcer Report</strong></p>
                        <i class="fas fa-file-alt fa-5x text-info mb-3"></i>
                        <h5 class="card-title">Report</h5>
                        <br><br>
                        <p class="card-text">Download the latest report by clicking the button below.</p>
                        <a href="enforcer_report_pdf.php" class="btn btn-primary">Download Report</a>
                    </div>
                    <div class="col card mx-2  py-2">
                        <p><strong>Resident Report</strong></p>
                        <i class="fas fa-file-alt fa-5x text-warning mb-3"></i>
                        <h5 class="card-title">Report</h5>
                        <br><br>
                        <p class="card-text">Download the latest report by clicking the button below.</p>
                       
                        <a href="resident_report_pdf.php" class="btn btn-primary">Download Report</a>
                    </div>
                    <div class="col card mx-2  py-2">
                        <div class="form-group">
                            <p><strong>Accomplishment Report</strong></p>
                            <i class="fas fa-file-alt fa-5x text-danger mb-3"></i>
                            <h5 class="card-title">Select Month</h5>
                            <!-- Year Dropdown -->
                            <select id="year-select" class="form-control mb-2">
                                <option value="" disabled selected>Select a Year</option>
                                <?php
                                // Get the current year
                                $currentYear = date('Y');
                                // Generate options for the past 10 years
                                for ($year = $currentYear - 10; $year <= $currentYear; $year++) {
                                    echo "<option value=\"$year\">$year</option>";
                                }
                                ?>
                            </select>
                            <select id="month-select" class="form-control mb-2">
                                <option value="" disabled selected>Select a month</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            <p class="card-text">Download the latest report by clicking the button below.</p>
                            <button id="download-btn" class="btn btn-primary w-100">Download Report</button>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('download-btn').addEventListener('click', function () {
        const monthSelect = document.getElementById('month-select');
        const selectedMonth = monthSelect.value;

        const yearSelect = document.getElementById('year-select');
        const selectedYear = yearSelect.value;

        if (!selectedMonth && !selectedYear) {
            alert('Please select a month.');
            return;
        }

        // Redirect to the PHP script with the selected month as a query parameter
        window.location.href = `accomplishment_report_pdf.php?month=${selectedMonth}&year=${selectedYear}`;
    });
</script>

<?php
require_once("templates/footer.php");
?>

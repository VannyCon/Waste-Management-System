<?php
session_start(); // Start the session
include_once('../config/config.php');
// Check if the 'resident' session is not set or is false
if (!isset($_SESSION['enforcer']) || $_SESSION['enforcer'] !== true || isset($_SESSION['enforcer_id']) === false) {
    // Redirect to the login page if the user is not authenticated
    header("Location: enforcer_login.php");
    exit();

}

$title = "Violation Reports";

$reports = [];

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the query
    $query = "
        SELECT 
            `id`, 
            `violationID`, 
            `resident_name`,
            `violators_name`,
            `description`, 
            `violators_location`,
            `latitude`, 
            `longitude`, 
            `date`, 
            `time`, 
            `admin_approval`, 
            `isActive` 
        FROM 
            `tbl_resident_report` 
        WHERE 
            `isActive` = 1 
            AND `admin_approval` = 1 
            AND `enforcer_id` = :enforcerID
    ";

    $stmt = $pdo->prepare($query);

    // Bind the enforcer ID to the query
    $stmt->bindParam(':enforcerID', $_SESSION['enforcer_id'], PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch all matching records
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">
    <div class=" d-flex justify-content-between"> 
        <a href="create.php" class="btn btn-success mt-3">Create</a>
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>

       
       
    </div>

        <h2 class="text-center mb-4">Violation Reports</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Violation ID</th>
                    <th>Resident Name</th>
                    <th>Violators Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reports): ?>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['id']) ?></td>
                            <td><?= htmlspecialchars($report['violationID']) ?></td>
                            <td><?= htmlspecialchars($report['resident_name']) ?></td>
                            <td><?= htmlspecialchars($report['violators_name']) ?></td>
                            <td><?= htmlspecialchars($report['description']) ?></td>
                            <td><?= htmlspecialchars($report['violators_location']) ?></td>
                            <td><?= htmlspecialchars($report['date']) ?></td>
                            <td><?= htmlspecialchars($report['time']) ?></td>
                            <td><?= $report['isActive'] ? 'Active' : 'Inactive' ?></td>
                            <td><a href="violator_location.php?violationID=<?php echo $report['violationID'] ?>&residentName=<?php echo $report['resident_name']?>&latitude=<?php echo $report['latitude']?>&longitude=<?php echo $report['longitude']?>&description=<?php echo $report['description']?>" type="button" class="btn btn-primary w-100">Take</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No reports found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
session_start();
include_once('../../config/config.php');
// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}




// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = "user";
require_once("templates/headers.php");
require_once("templates/nav.php");

// Fetch records from tbl_enforcer_report, ordered by ID in ascending order
// Fetch records and calculate the total penalty
$sql = "SELECT `id`, `violationID`, `resident_name`, `violators_name`, `violators_age`, 
               `violators_gender`, `violators_location`, `violation_type`, `datetime`, 
               `latitude`, `longitude`, `penalty` 
        FROM `tbl_enforcer_report` 
        WHERE `isPaid` = 1
        ORDER BY `id` ASC";

$result = $conn->query($sql);

// Calculate the total penalty
$totalPenalty = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalPenalty += $row['penalty'];
    }
    // Reset the result pointer for fetching rows again in the table
    $result->data_seek(0);
}
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><b style="color: black;">Total Paid</b></h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="EnforcerReports" class="table table-striped table-responsive-sm" style="min-width: 845px">
                    <thead>
                        <tr>
                            <th><b style="color: black;">No.</th>
                            <th><b style="color: black;">Resident Name</th>
                            <th><b style="color: black;">Violator's Name</th>
                            <th><b style="color: black;">Violator's Age</th>
                            <th><b style="color: black;">Violator's Gender</th>
                            <th><b style="color: black;">Location</th>
                            <th><b style="color: black;">Violation Type</th>
                            <th><b style="color: black;">Date & Time</th>
                            <th><b style="color: black;">Penalty</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        <?php
                        $counter = 1; // Initialize a counter variable
                        if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td> <!-- Use the counter for ordered sequence -->
                                    <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violators_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violators_age']); ?></td>
                                    <td><?php echo ucfirst($row['violators_gender']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violators_location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violation_type']); ?></td>
                                    <td><?php echo date('M j, Y, g:i a', strtotime($row['datetime'])); ?></td>
                                    <td>&#8369;<?php echo number_format($row['penalty'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <!-- Total Penalty Row -->
                    <tfoot>
                        <tr class="text-dark">
                            <td colspan="8" class="text-right"><strong>Total Penalty:</strong></td>
                            <td><strong class="text-success">&#8369;<?php echo number_format($totalPenalty, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Large Image Modal -->
<div class="modal fade" id="largeImageModal" tabindex="-1" role="dialog" aria-labelledby="largeImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="largeImageModalLabel">Full Size Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="largeImage" src="" alt="Full size image" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Enforcer Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateForm" method="POST" action="enforcer_update.php">
                    <input type="hidden" name="id" id="updateId">

                    <div class="form-group">
                        <label for="updateName">Violator's Name</label>
                        <input type="text" class="form-control" name="name" id="updateName" required>
                    </div>
                    <div class="form-group">
                        <label for="updateAge">Age</label>
                        <input type="number" class="form-control" name="age" id="updateAge" required>
                    </div>
                    <div class="form-group">
                        <label for="updateGender">Gender</label>
                        <select class="form-control" name="gender" id="updateGender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateLocation">Location</label>
                        <input type="text" class="form-control" name="location" id="updateLocation" required>
                    </div>
                    <div class="form-group">
                        <label for="updateType">Violation Type</label>
                        <input type="text" class="form-control" name="type" id="updateType" required>
                    </div>
                    <div class="form-group">
                        <label for="updatePenalty">Penalty</label>
                        <input type="number" class="form-control" name="penalty" id="updatePenalty" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Enforcer Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
                <form id="deleteForm" method="POST" action="enforcer_delete.php">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="paidModal" tabindex="-1" role="dialog" aria-labelledby="paidModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paidModalLabel">Pay Violation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to violators paid?</p>
                <form id="paidForm" method="POST" action="paid_update.php">
                    <input type="hidden" name="id" id="paidID">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


<script>
function showImage(imageSrc) {
    $('#largeImage').attr('src', imageSrc);
    $('#largeImageModal').modal('show');
}

// Clean up when any images modal is closed
$('[id^="imagesModal-"]').on('hidden.bs.modal', function () {
    const violationId = $(this).attr('id').split('-')[1];
    $(`#imageGallery-${violationId}`).empty();
});

// Populate Update Modal
$('#updateModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#updateId').val(button.data('id'));
    $('#updateName').val(button.data('name'));
    $('#updateAge').val(button.data('age'));
    $('#updateGender').val(button.data('gender'));
    $('#updateLocation').val(button.data('location'));
    $('#updateType').val(button.data('type'));
    $('#updatePenalty').val(button.data('penalty'));
});

// Populate Delete Modal
$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#deleteId').val(button.data('id'));
});

// Populate Delete Modal
$('#paidModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#paidID').val(button.data('id'));
});

</script>

<?php
$conn->close();
require_once("templates/footer.php");
?>
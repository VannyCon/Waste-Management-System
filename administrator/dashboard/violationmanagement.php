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

$title = "Enforcement Records";
require_once("templates/headers.php");
require_once("templates/nav.php");

// Handle update or delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        // Update record
        $id = $_POST['id'];
        $enforcer_name = $_POST['enforcer_name'];
        $name_of_violator = $_POST['name_of_violator'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $location = $_POST['location'];
        $types_of_violation = $_POST['types_of_violation'];
        $penalty = $_POST['penalty'];

        // Prepare the update statement
        $stmt = $conn->prepare("UPDATE violation_management SET Enforcer_Name=?, Name_of_Violator=?, Age=?, Gender=?, Location=?, Types_of_Violation=?, Penalty=? WHERE id=?");
        $stmt->bind_param("ssissssi", $enforcer_name, $name_of_violator, $age, $gender, $location, $types_of_violation, $penalty, $id);
        
        if ($stmt->execute()) {
            // Return updated data as JSON
            echo json_encode([
                'status' => 'success',
                'id' => $id,
                'enforcer_name' => $enforcer_name,
                'name_of_violator' => $name_of_violator,
                'age' => $age,
                'gender' => $gender,
                'location' => $location,
                'types_of_violation' => $types_of_violation,
                'penalty' => $penalty
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating record: ' . $stmt->error]);
        }

        $stmt->close();
        exit(); // Exit to prevent the rest of the script from running
    }
} elseif (isset($_GET['delete_id'])) {
    // Delete record
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM violation_management WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'id' => $id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete record']);
    }
    $stmt->close();
    exit();
}
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><b style="color: black;">VIOLATION MANAGEMENT</b></h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="Enforcement" class="table table-striped table-responsive-sm" style="min-width: 845px">
                    <thead>
                        <tr>
                            <th><b style="color: black;">No.</b></th>
                            <th><b style="color: black;">Enforcer Name</b></th>
                            <th><b style="color: black;">Name Of Violator</b></th>
                            <th><b style="color: black;">Age</b></th>
                            <th><b style="color: black;">Gender</b></th>
                            <th><b style="color: black;">Location</b></th>
                            <th><b style="color: black;">Types Of Violation</b></th>
                            <th><b style="color: black;">Date & Time</b></th>
                            <th><b style="color: black;">Latitude</b></th>
                            <th><b style="color: black;">Longitude</b></th>
                            <th><b style="color: black;">Evidence</b></th>
                            <th><b style="color: black;">Penalty</b></th>
                            <th><b style="color: black;">Actions</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM violation_management ORDER BY DateTime DESC");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr id='row-{$row['id']}'>";
                                echo "<td><b style='color: black;'>{$no}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Enforcer_Name']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Name_of_Violator']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Age']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Gender']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Location']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Types_of_Violation']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['DateTime']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Latitude']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Longitude']}</b></td>";
                                echo "<td><button class='btn btn-info' onclick='viewEvidence(\"uploads/{$row['Evidence']}\")'><i class='fa fa-image'></i></button></td>";
                                echo "<td><b style='color: black;'>{$row['Penalty']}</b></td>";
                                echo "<td>
                                <button class='btn btn-warning' onclick='openEditModal(". json_encode($row) .")'><i class='fas fa-edit'></i> Edit</button>
                                <button class='btn btn-danger' onclick='confirmDelete({$row['id']})'><i class='fas fa-trash'></i> Delete</button>
                              </td>";
                                echo "</tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='13'><b style='color: black;'>No enforcement records found</b></td></tr>";
                        }

                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Evidence Modal -->
<div class="modal fade" id="viewEvidenceModal" tabindex="-1" role="dialog" aria-labelledby="viewEvidenceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEvidenceLabel"><b style="color: black;">View Evidence</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="evidence" src="" class="img-fluid" alt="Evidence">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b style="color: black;">Close</b></button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel"><b style="color: black;">Edit Violation Record</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-group">
                        <label for="editEnforcerName"><b style="color: black;">Enforcer Name</b></label>
                        <input type="text" class="form-control" name="enforcer_name" id="editEnforcerName" required>
                    </div>
                    <div class="form-group">
                        <label for="editNameOfViolator"><b style="color: black;">Name Of Violator</b></label>
                        <input type="text" class="form-control" name="name_of_violator" id="editNameOfViolator" required>
                    </div>
                    <div class="form-group">
                        <label for="editAge"><b style="color: black;">Age</b></label>
                        <input type="number" class="form-control" name="age" id="editAge" required>
                    </div>
                    <div class="form-group">
                        <label for="editGender"><b style="color: black;">Gender</b></label>
                        <select class="form-control" name="gender" id="editGender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editLocation"><b style="color: black;">Location</b></label>
                        <input type="text" class="form-control" name="location" id="editLocation" required>
                    </div>
                    <div class="form-group">
                        <label for="editTypesOfViolation"><b style="color: black;">Types Of Violation</b></label>
                        <input type="text" class="form-control" name="types_of_violation" id="editTypesOfViolation" required>
                    </div>
                    <div class="form-group">
                        <label for="editPenalty"><b style="color: black;">Penalty</b></label>
                        <input type="text" class="form-control" name="penalty" id="editPenalty" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><b style="color: black;">Save Changes</b></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel"><b style="color: black;">Success!</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><b style="color: black;">The record has been successfully updated.</b></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b style="color: black;">Close</b></button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle edit and delete functionality -->
<script>
    // Open the edit modal and fill in the data
    function openEditModal(row) {
        document.getElementById('editId').value = row.id;
        document.getElementById('editEnforcerName').value = row.Enforcer_Name;
        document.getElementById('editNameOfViolator').value = row.Name_of_Violator;
        document.getElementById('editAge').value = row.Age;
        document.getElementById('editGender').value = row.Gender;
        document.getElementById('editLocation').value = row.Location;
        document.getElementById('editTypesOfViolation').value = row.Types_of_Violation;
        document.getElementById('editPenalty').value = row.Penalty;
        $('#editModal').modal('show');
    }

    // Handle the form submission for editing
    document.getElementById('editForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this);

        // Send the form data via AJAX
        fetch('violationmanagement.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                $('#editModal').modal('hide');
                // Show the success modal
                $('#successModal').modal('show');
                
                // Update the row without reloading the page
                let row = document.getElementById('row-' + data.id);
                row.cells[1].innerText = data.enforcer_name;
                row.cells[2].innerText = data.name_of_violator;
                row.cells[3].innerText = data.age;
                row.cells[4].innerText = data.gender;
                row.cells[5].innerText = data.location;
                row.cells[6].innerText = data.types_of_violation;
                row.cells[11].innerText = data.penalty;
            } else {
                alert('Error: ' + data.message); // Show error message
            }
        })
        .catch(error => console.log('Error:', error));
    });

    // Function to confirm and delete a record
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this record?")) {
            fetch('violationmanagement.php?delete_id=' + id, {
                method: 'GET',
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Dynamically remove the row from the table
                    let row = document.getElementById('row-' + data.id);
                    row.parentNode.removeChild(row);
                } else {
                    console.error('Error deleting record:', data.message);
                }
            })
            .catch(error => console.log('Error:', error));
        }
    }

    // Function to view evidence in modal
    function viewEvidence(evidencePath) {
        document.getElementById('evidence').src = evidencePath;
        $('#viewEvidenceModal').modal('show');
    }
</script>

<?php
require_once("templates/footer.php");
?>


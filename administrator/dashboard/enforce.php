<?php
session_start();
include_once("../../config/config.php");
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

$showAddPopup = false;
$showEditPopup = false;
$showDeletePopup = false;
$searchTerm = '';
$deleteId = null;

// Handle the form submission for adding a new user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    var_dump($_POST);  // Add this line to debug
    // Sanitize and retrieve input data
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $age = intval($_POST['age']);
    $gender = trim($_POST['gender']);
    $contactnumber = trim($_POST['contactnumber']);
    $address = trim($_POST['address']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($fullname) || empty($email) || empty($gender) || empty($address) || empty($username) || empty($password)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
    } else {
        // Prepare the SQL statement to insert the data
        $stmt = $conn->prepare("INSERT INTO enforcers (Fullname, email, Age, Gender, Contact_number, Address, Username, Password, Created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssisisss", $fullname, $email, $age, $gender, $contactnumber, $address, $username, $password);

        // Execute the statement and handle success or failure
        if ($stmt->execute()) {
            $showAddPopup = true;
        } else {
            error_log("Database Error: " . $stmt->error);
            echo "<script>alert('Error adding user: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}


// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_stmt = $conn->prepare("DELETE FROM enforcers WHERE id = ?");
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        $showDeletePopup = true;
    } else {
        echo "<script>alert('Error deleting user: " . $delete_stmt->error . "');</script>";
    }
    $delete_stmt->close();
}

// Handle the form submission for editing a user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $fullname = trim($_POST['edit_fullname']);
    $email = $_POST['edit_email'];
    $age = intval($_POST['edit_age']);
    $gender = trim($_POST['edit_gender']);
    $contactnumber = trim($_POST['edit_contactnumber']);
    $address = trim($_POST['edit_address']);
    $username = trim($_POST['edit_username']);
    $password = trim($_POST['edit_password']);



    $edit_stmt = $conn->prepare("UPDATE enforcers SET Fullname = ?, email = ?, Age = ?, Gender = ?, Contact_number = ?, Address = ?, Username = ?, Password = ? WHERE id = ?");
    $edit_stmt->bind_param("ssisisssi", $fullname, $email, $age, $gender, $contactnumber, $address, $username, $password, $id);

    if ($edit_stmt->execute()) {
        $showEditPopup = true; // Trigger the edit pop-up
    }

    $edit_stmt->close();
}

// Handle search
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $searchTerm = trim($_POST['search_term']);
}

$title = "Enforcers";
require_once("templates/headers.php");
require_once("templates/nav.php");
?>

<!-- Success Message Pop-up for Adding User -->
<div id="successAddPopup" class="popup" style="<?php echo $showAddPopup ? 'display: block;' : 'display: none;'; ?>">
    <div class="popup-content">
        <h2>Enforcer added successfully!</h2>
        <img src="https://img.icons8.com/ios/50/00FF00/checkmark.png" alt="Checkmark" class="checkmark-icon"/>
    </div>
</div>

<!-- Success Message Pop-up for Editing User -->
<div id="successEditPopup" class="popup" style="<?php echo $showEditPopup ? 'display: block;' : 'display: none;'; ?>">
    <div class="popup-content">
        <h2>Update successfully!</h2>
        <img src="https://img.icons8.com/ios/50/00FF00/checkmark.png" alt="Checkmark" class="checkmark-icon"/>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>


<script>
    let deleteId = null;

    function showDeleteModal(id) {
        deleteId = id;
        $('#deleteConfirmationModal').modal('show');
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            window.location.href = `?delete_id=${deleteId}`;
        }
    });

    function hidePopups() {
        setTimeout(function() {
            document.getElementById('successAddPopup').style.display = 'none';
            document.getElementById('successEditPopup').style.display = 'none';
            document.getElementById('successDeletePopup').style.display = 'none';
        }, 2000);
    }

    <?php if ($showAddPopup || $showEditPopup || $showDeletePopup): ?>
        hidePopups();
    <?php endif; ?>
</script>

<style>
    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        text-align: center;
        width: 300px;
        border-radius: 8px;
    }

    .popup-content h2 {
        color: #4C3D91;
        font-size: 20px;
        margin-bottom: 15px;
    }

    .checkmark-icon {
        width: 50px;
        height: 50px;
        margin-top: 10px;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><b style="color: black;">ENFORCERS</b></h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="POST" action="">
                        <div class="input-group">
                            <input type="text" name="search_term" class="form-control" placeholder="Search by name..." value="<?php echo htmlspecialchars($searchTerm); ?>" onfocus="clearPlaceholder(this)" onblur="restorePlaceholder(this)" oninput="filterTable(this.value)">
                            <div class="input-group-append">
                                <button type="submit" name="search" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <!-- Add User Button -->
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addUserModal">Add Enforcer</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="User" class="table table-striped table-responsive-sm" style="min-width: 845px">
                    <thead>
                        <tr>
                            <th><b style="color: black;">No.</th>
                            <th><b style="color: black;">Full Name</th>
                            <th><b style="color: black;">Email</th>
                            <th><b style="color: black;">Age</th>
                            <th><b style="color: black;">Gender</th>
                            <th><b style="color: black;">Contact Number</th>
                            <th><b style="color: black;">Address</th>
                            <th><b style="color: black;">Username</th>
                        
                            <th><b style="color: black;">Date Registered</th>
                            <th><b style="color: black;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Modify the SQL query to include search functionality
                        $sql = "SELECT * FROM enforcers";
                        if ($searchTerm) {
                            $sql .= " WHERE Fullname LIKE ?";
                            $stmt = $conn->prepare($sql);
                            $searchParam = "%$searchTerm%";
                            $stmt->bind_param("s", $searchParam);
                        } else {
                            $stmt = $conn->prepare($sql);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><b style='color: black;'>{$no}</td>";
                                echo "<td><b style='color: black;'>{$row['Fullname']}</td>";
                                echo "<td><b style='color: black;'>{$row['email']}</td>";
                                echo "<td><b style='color: black;'>{$row['Age']}</td>";
                                echo "<td><b style='color: black;'>{$row['Gender']}</td>";
                                echo "<td><b style='color: black;'>{$row['Contact_number']}</td>";
                                echo "<td><b style='color: black;'>{$row['Address']}</td>";
                                echo "<td><b style='color: black;'>{$row['Username']}</td>";
                               
                                echo "<td><b style='color: black;'>{$row['Created_at']}</td>";
                                echo "<td>
                                        <button class='btn btn-warning' data-toggle='modal' data-target='#editUserModal{$row['id']}'>Edit</button>
                                        <a href='?delete_id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a>
                                      </td>";
                                echo "</tr>";

                                // Edit User Modal
                                echo "
                                <div class='modal fade' id='editUserModal{$row['id']}' tabindex='-1' role='dialog' aria-labelledby='editUserModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='editUserModalLabel'>Edit Enforcer</h5>
                                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                    <span aria-hidden='true'>&times;</span>
                                                </button>
                                            </div>
                                            <div class='modal-body'>
                                                <form method='POST' action=''>
                                                    <input type='hidden' name='id' value='{$row['id']}'>
                                                    <div class='form-group'>
                                                        <label for='edit_fullname'>Full Name</label>
                                                        <input type='text' class='form-control' name='edit_fullname' value='{$row['Fullname']}' required>
                                                    </div>
                                                     <div class='form-group'>
                                                        <label for='edit_email'>Email</label>
                                                        <input type='email' class='form-control' name='edit_email' value='{$row['email']}' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='edit_age'>Age</label>
                                                        <input type='number' class='form-control' name='edit_age' value='{$row['Age']}' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='edit_gender'>Gender</label>
                                                        <input type='text' class='form-control' name='edit_gender' value='{$row['Gender']}' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='edit_contactnumber'>Contact Number</label>
                                                        <input type='text' class='form-control' name='edit_contactnumber' value='{$row['Contact_number']}' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='edit_address'>Address</label>
                                                        <input type='text' class='form-control' name='edit_address' value='{$row['Address']}' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='edit_username'>Username</label>
                                                        <input type='text' class='form-control' name='edit_username' value='{$row['Username']}' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='edit_password'>Password</label>
                                                        <input type='password' class='form-control' name='edit_password' placeholder='password'
                                                        >
                                                    </div>
                                                    <button type='submit' name='edit_user' class='btn btn-warning'>Update User</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='10' style='text-align: center; color: black;'>No records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add Enforcer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" class="form-control" name="fullname" required>
                        </div>
                       
                        <div class="form-group">
                            <label for="asdfds">test</label>
                            <input type="text" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" name="age" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <input type="text" class="form-control" name="gender" required>
                        </div>
                        <div class="form-group">
                            <label for="contactnumber">Contact Number</label>
                            <input type="text" class="form-control" name="contactnumber" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $conn->close(); ?>
</div>

<script>
    function filterTable(searchTerm) {
        const table = document.getElementById('User');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }
    }

    function clearPlaceholder(input) {
        input.placeholder = '';
    }

    function restorePlaceholder(input) {
        if (input.value === '') {
            input.placeholder = 'Search by name...';
        }
    }
</script>

<?php require_once("templates/footer.php"); ?>
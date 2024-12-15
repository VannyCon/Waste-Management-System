<?php 
include('../../config/config.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch records
$sql = "SELECT `id`, `violationID`, `resident_name`, `violators_name`, `description`, `violators_location`, `latitude`, `longitude`, `date`, `time`, `admin_approval`, `isActive` FROM `tbl_resident_report` WHERE `isActive` = 1";
$result = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>WASTE MANAGEMENT VIOLATION MONITORING SYSTEM WITH MAPPING</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/jpg" sizes="16x16" href="./images/logo.jpg">
    <link href="./vendor/pg-calendar/css/pignose.calendar.min.css" rel="stylesheet">
    <link href="./vendor/chartist/css/chartist.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">

    <!-- Datatable -->
    <link href="./vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- sweet alert -->
    <link href="./vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start logo-compact logo-title
        ***********************************-->
        <div class="nav-header">
                <img class="logo-abbr" src="./images/logo.jpg" alt="">
                <img class="logo-compact" src="./images/logo-text.png" alt="">
                <span style="margin-left:10px;">Barangay-Old Sagay</span>
            </a>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                        </div>
                        <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h5><b>WASTE MANAGEMENT VIOLATION MONITORING SYSTEM</b></h5>
                    <p class="mb-0"></p>
                </div>
            </div>

            <ul class="navbar-nav header-right">
                <li class="nav-item dropdown notification_dropdown">
                    <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                    <div class="notification-container">    
                    <i class="icon-bell 
                    <?php if ($result->num_rows > 0){ ?>

                    text-danger
                    <?php }?>
                    "></i>
                        <span class="notification-badge"> <?php echo $result->num_rows; ?></span>
                    </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <ul class="list-unstyled">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <li class="media dropdown-item">
                                    <span class="danger"><i class="ti-user"></i></span>
                                    <div class="media-body">
                                        <a href="resident_report.php">
                                            <p>Report: <strong><?php echo $row['resident_name']; ?></strong> 
                                            <?php echo $row['description']; ?>
                                            </p>
                                        </a>
                                    </div>
                                    <span class="notify-time"><?php echo $row['date']; ?> <?php echo date("g:i A", strtotime($row['time'])); ?>
                                    </span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="media dropdown-item">
                                <p>No Violation Found</p>
                            </li>
                        <?php endif; ?>

                            
                        </ul>
                    </div>
                </li>
                    <li class="nav-item dropdown header-profile">
                    <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                    <i class="mdi mdi-account"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                    <a href="admin_profile.php" class="dropdown-item">
                        <i class="mdi mdi-account-edit"></i>
                        <span class="ml-2">Edit Profile</span>
                    </a>
                    <a href="../logout.php" class="dropdown-item">
                        <i class="icon-key"></i>
                        <span class="ml-2">Logout</span>
                    </a>
                    </div>
                </li>
            </ul>
                    </div>
                </nav>
            </div>
        </div>
         <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

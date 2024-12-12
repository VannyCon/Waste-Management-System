  <!--**********************************
            Footer start
        ***********************************-->
        <!--**********************************
            Footer end
        ***********************************-->

        <!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="./vendor/global/global.min.js"></script>
    <script src="./js/quixnav-init.js"></script>
    <script src="./js/custom.min.js"></script>

    <?php
        if($title=="dashboard") {
            echo "<script src='./vendor/chartist/js/chartist.min.js'></script>";
            echo "<script src='./vendor/moment/moment.min.js'></script>";
            echo "<script src='./vendor/pg-calendar/js/pignose.calendar.min.js'></script>";
            echo "<script src='./js/dashboard/dashboard-2.js'></script>";
        }
        else if($title=="mapview"){
            echo "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo "<script src='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
            echo "<script src='./js/scripts/mapview.js'></script>";
        }
        else if ($title=="books"){
            echo  "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo  "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo  "<script src ='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
            echo  "<script src ='./js/scripts/books.js'></script>";
          }
          else if ($title=="borrowers"){
            echo  "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo  "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo   "<script src ='./js/scripts/borrowers.js'></script>";
            echo   "<script src ='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
          }
          else if ($title=="borrowedbooks"){
            echo  "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo  "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo  "<script src ='./js/scripts/borrowedbooks.js'></script>";
            echo  "<script src ='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
          }
          else if ($title=="report"){
            echo  "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo  "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo   "<script src ='./js/scripts/report.js'></script>";
            echo   "<script src ='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
          }
          else if ($title=="user"){
            echo  "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo  "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo   "<script src ='./js/scripts/user.js'></script>";
            echo   "<script src ='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
          }else if ($title=="paided_violation"){
            echo  "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo  "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo   "<script src ='./js/scripts/user.js'></script>";
            echo   "<script src ='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
          }
          else if ($title=="enforcer"){
            echo  "<script src='./vendor/datatables/js/jquery.dataTables.min.js'></script>";
            echo  "<script src='./js/plugins-init/datatables.init.js'></script>";
            echo   "<script src ='./js/scripts/user.js'></script>";
            echo   "<script src ='./vendor/sweetalert2/dist/sweetalert2.min.js'></script>";
          }
          
          
          
    ?>
</body>

</html>
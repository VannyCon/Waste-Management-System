$(document).ready(function(){
    //btnAdd click listener
    $("#btnAdd").click(function(){
        $("#btnSave").text("Save Changes");
        $("#frmAdd")[0].reset();
        $("#modUser").modal("show");
    });

    //btnEdit
    $("#tblUser").on("click", ".edit", function(){
        $("#btnSave").text("Update Changes");
        $("#cid").val($(this).data('id'));

        var currentRow = $(this).closest('tr');
        $("#id").val(currentRow.find('td:eq(0)').text());
        $("#username").val(currentRow.find('td:eq(1)').text());
        $("#password").val(currentRow.find('td:eq(2)').text());
        $("#email").val(currentRow.find('td:eq(3)').text());
        $("#modUser").modal("show");
    });

    //btnDelete
    $("#tblUser").on("click", ".delete", function(){
        var cid = $(this).data('id');
        swal({
            title: "Are you sure to delete?",
            text: "You will not be able to recover this imaginary file !!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6855",
            confirmButtonText: "Yes, delete it !!",
        }).then(result => {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: "includes/manageUsers.php",
                    data: { id:cid, action: 'deldata'},
                    dataType: "json",
                    success: function(response){
                        if(response.success){
                            swal({
                                title: "Well Done!",
                                text: response.message,
                                type: "success",
                                confirmButtonColor: "#57a94f",
                            });
                        }
                        else{
                            swal({
                                title: "Error!",
                                text: response.message,
                                type: "error",
                                confirmButtonColor: "#57a94f",
                            });
                        }
                        //reload
                        loadData();
                    }
                });
            } else if (result.dismiss === swal.DismissReason.cancel) {
                swal("Cancelled", "Your imaginary file is safe:)", "error");
            }
        });
    });

    function loadData() {
        $.ajax({
            type: "POST",
            url: "includes/manageUsers.php",
            data: {action: "fetch"},
            dataType: "html",
            success: function(response){
               if ($.fn.DataTable.isDataTable('#tblUser')){
                    $('#tblUser').DataTable().clear().destroy();
               }
               $("#tblUser tbody").html(response);
               $('#tblUser').DataTable({
                    createdRow: function(row, data, dataIndex){
                        $(row).addClass('selected');
                    },
               });
            }
        });
    }
    loadData();

    $("#frmAdd").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();

        if($("#btnSave").text() == "Save Changes"){
            //code for saving record
            formData += "&action=addRec";
        } else {
            //code for updating record
            formData += "&action=updRec";
        }

        $.ajax({
            type: "POST",
            url: "includes/manageUsers.php",
            data: formData,
            dataType: "json",
            success: function(response){
                if(response.success){
                    swal({
                        title: "Well Done!",
                        text: response.message,
                        type: "success",
                        confirmButtonColor: "#57a94f",
                    });
                }
                else{
                    swal({
                        title: "Error!",
                        text: response.message,
                        type: "error",
                        confirmButtonColor: "#57a94f",
                    });
                }
                //reload
                $("#modUser").modal("hide");
                loadData();
            }
        });
    });
});

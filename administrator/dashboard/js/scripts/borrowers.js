$(document).ready(function() {

    $("#btnAdd").click(function() {
      $("#btnSave").text("Save Changes");
      $("#frmAdd")[0].reset();
      $("#modBorrowers").modal("show");
    });
  
  //btnEdit
    $("#tblBorrowers").on("click", ".edit", function() {
      $("#btnSave").text("Update Changes");
      $("#cid").val($(this).data('id'));
  
  
      var currentRow = $(this).closest('tr');
          $("#borrowersId").val(currentRow.find('td:eq(0)').text());
          $("#borrowers_name").val(currentRow.find('td:eq(1)').text());
          $("#course").val(currentRow.find('td:eq(2)').text());
          $("#book_name").val(currentRow.find('td:eq(3)').text());
          $("#borrowed_date").val(currentRow.find('td:eq(4)').text());
          $("#returned_date").val(currentRow.find('td:eq(5)').text());
          $("#address").val(currentRow.find('td:eq(6)').text());

          $("#modBorrowers").modal("show");
      });
  //btnDelete
    $("#tblBorrowers").on("click",".delete", function() {
      var cid = $(this).data('id');
      swal({
        title: "Are you are to delete ?",
        text: "You will not be able to recover this imaginary file !!",
        type: "warning",
        showCancelButton: !0,
        confirmButtonColor: "#DD6855",
        confirmButtonText: "Yes, delete it !!",
      }).then(result => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "includes/manageBorrowers.php",
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
         })
        }
        else if (result.dismiss === swal.DismissReason.cancel) {
            swal("Cancelled", "Your imaginary file is safe:)", "error");
        }
        swall.closeModal();
    });
  });
  
  function loadData() {
    $.ajax({
        type: "POST",
        url: "includes/manageBorrowers.php",
        data: {action: "fetch"},
        dataType: "html",
        success: function(response){
           if ($.fn.DataTable.isDataTable('#tblBorrowers')){
  
            $('#tblBorrowers').DataTable().clear().destroy();
           }
           $("#tblBorrowers tbody").html(response);
           $('#tblBorrowers').DataTable({
            createdRow: function(row, data, dataIndex){
                $(row).addClass('selected');
            },
           });
        }
    })
  }
  loadData();
  
  $("#frmAdd").submit(function(e){
    e.preventDefault();
    var formData = $(this).serialize();
  
    if($("#btnSave").text() == "Save Changes"){
    //code for saving record
    formData += "&action=addRec";
    $.ajax({
        type: "POST",
        url: "includes/manageBorrowers.php",
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
            $("#modBorrowers").modal("hide");
            loadData();
        }
  })
  }
  else {
  formData += "&action=updRec";
  $.ajax({
    type: "POST",
    url: "includes/manageBorrowers.php",
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
        $("#modBorrowers").modal("hide");
        loadData();
    }
  })
  }
  });
  })
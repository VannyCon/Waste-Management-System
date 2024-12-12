$(document).ready(function() {

    $("#btnAdd").click(function() {
      $("#btnSave").text("Save Changes");
      $("#frmAdd")[0].reset();
      $("#modReport").modal("show");
    });
  
  //btnEdit
    $("#tblReport").on("click", ".edit", function() {
      $("#btnSave").text("Update Changes");
      $("#cid").val($(this).data('id'));
  
  
      var currentRow = $(this).closest('tr');
          $("#borrowerId").val(currentRow.find('td:eq(0)').text());
          $("#bookId").val(currentRow.find('td:eq(1)').text());
          $("#borrower_name").val(currentRow.find('td:eq(2)').text());
          $("#course").val(currentRow.find('td:eq(3)').text());
          $("#booktitle").val(currentRow.find('td:eq(4)').text());
          $("#borrow_date").val(currentRow.find('td:eq(5)').text());
          $("#due_date").val(currentRow.find('td:eq(6)').text());
          $("#address").val(currentRow.find('td:eq(7)').text());

          $("#modReport").modal("show");
      });
  //btnDelete
    $("#tblReport").on("click",".delete", function() {
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
                url: "includes/manageReport.php",
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
        url: "includes/manageReport.php",
        data: {action: "fetch"},
        dataType: "html",
        success: function(response){
           if ($.fn.DataTable.isDataTable('#tblReport')){
  
            $('#tblReport').DataTable().clear().destroy();
           }
           $("#tblReport tbody").html(response);
           $('#tblReport').DataTable({
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
        url: "includes/manageReport.php",
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
            $("#modReport").modal("hide");
            loadData();
        }
  })
  }
  else {
  formData += "&action=updRec";
  $.ajax({
    type: "POST",
    url: "includes/manageReport.php",
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
        $("#modReport").modal("hide");
        loadData();
    }
  })
  }
  });
  })
  
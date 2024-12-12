$(document).ready(function() {

    $("#btnAdd").click(function() {
      $("#btnSave").text("Save Changes");
      $("#frmAdd")[0].reset();
      $("#modBooks").modal("show");
    });
  
  //btnEdit
    $("#tblBooks").on("click", ".edit", function() {
      $("#btnSave").text("Update Changes");
      $("#cid").val($(this).data('id'));
  
  
      var currentRow = $(this).closest('tr');
          $("#book_name").val(currentRow.find('td:eq(0)').text());
          $("#author").val(currentRow.find('td:eq(1)').text());
          $("#category").val(currentRow.find('td:eq(2)').text());
          $("#publisher").val(currentRow.find('td:eq(3)').text());
          $("#published_date").val(currentRow.find('td:eq(4)').text());
          $("#quantityavailable").val(currentRow.find('td:eq(5)').text());
          $("#totalquantity").val(currentRow.find('td:eq(6)').text());

          $("#modBooks").modal("show");
      });
  //btnDelete
    $("#tblBooks").on("click",".delete", function() {
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
                url: "includes/manageBooks.php",
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
        url: "includes/manageBooks.php",
        data: {action: "fetch"},
        dataType: "html",
        success: function(response) {
           if ($.fn.DataTable.isDataTable('#tblBooks')){
  
            $('#tblBooks').DataTable().clear().destroy();
           }
           $("#tblBooks tbody").html(response);
           $('#tblBooks').DataTable({
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
  
    if($("#btnSave").text() == "Save Changes") {
    //code for saving record
    formData += "&action=addRec";
  $.ajax({
      type: "POST",
      url: "includes/manageBooks.php",
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
          $("#modBooks").modal("hide");
          loadData();
      }
})
}
  else {
  formData += "&action=updRec";
  $.ajax({
    type: "POST",
    url: "includes/manageBooks.php",
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
        $("#modBooks").modal("hide");
        loadData();
    }
  })
  }
  });
  })
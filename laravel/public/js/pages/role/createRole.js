// Add new role Modal JS
//------------------------------------------------------------------
(function () {
 
  // reset form on modal hidden
  $('.modal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
  });

  // Select All checkbox click
  const selectAll = document.querySelector('#selectAll'),
    checkboxList = document.querySelectorAll('[type="checkbox"]');
  selectAll.addEventListener('change', t => {
    checkboxList.forEach(e => {
      e.checked = t.target.checked;
    });
  });

   // Select All checkbox click
  const updateSelectAll = document.querySelector('#updateSelectAll'),
    updateCheckboxList = document.querySelectorAll('[type="checkbox"]');
  updateSelectAll.addEventListener('change', t => {
    updateCheckboxList.forEach(e => {
      e.checked = t.target.checked;
    });
  });

  $("input[type='checkbox'].create-role-permission").change(function(){
   checkIfCreateCheckboxChecked();
});

  $("input[type='checkbox'].update-role-permission").change(function(){
   checkIfUpdateCheckboxChecked();
});

  function checkIfCreateCheckboxChecked(){
     var a = $("input[type='checkbox'].create-role-permission");
    if(a.length == a.filter(":checked").length){
        $("#selectAll").prop("checked",true);
    }else{
       $("#selectAll").prop("checked",false);

    }
  }

    function checkIfUpdateCheckboxChecked(){
     var a = $("input[type='checkbox'].update-role-permission");
    if(a.length == a.filter(":checked").length){
        $("#updateSelectAll").prop("checked",true);
    }else{
      $("#updateSelectAll").prop("checked",false);

    }
  }

  $("#create-role-button").click(function(){

        $("#title").removeClass("is-invalid");

  });



  $("#create-role").click(function(event){

  event.preventDefault();

  var data = $("#createRole").serializeArray();

  let url  = site_url + "/createRole";

  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "json",
    success: function(data) {
      $("#addRoleModal").modal('hide');
          toastr.success(data.message);
        $('#roleTable').dataTable().fnClearTable();
      $('#roleTable').dataTable().fnDraw();
      $('#roleTable').dataTable().fnDestroy();
          roleTableList();

                   },
     error: function (data) {
       if(data.responseJSON.statusCode =="403"){
         $("#addRoleModal").modal('hide');
          toastr.error(data.responseJSON.message);
      }
      var errors = data.responseJSON.errors;

       $.each(errors, function (key, value) 
          {
            $("#"+key).addClass("is-invalid");
            $("."+key+"_error").html(value);
          

          });

        

                
              }
  });
});



$(document).on("click",".edit-role",function(event){

    $("#update_title").removeClass("is-invalid");
    $("#pageloader").addClass("pageloader");

    event.preventDefault();

    var id = $(this).attr('data-id');

      var url  = site_url + "/role/" + id +"/edit";

  $.ajax({
    type: "GET",
    url: url,
    dataType: "json",
    success: function(response) {

      $("#update_title").val(response.data.title);
      $("#role_id").val(response.data.id);
      var rolePermissions = response.data.rolePermissions;

       $.each(rolePermissions, function (permissionId, rolePermission) 
          {
             $.each(rolePermission, function (key, value) 
          {

            if((key =="is_readable") && (value =="1")){
             $('#is_readable_update_'+permissionId).prop('checked', true);
            }
            if((key =="is_writable") && (value =="1")){
             $('#is_writable_update_'+permissionId).prop('checked', true);

            }if((key =="is_creatable") && (value =="1")){
             $('#is_creatable_update_'+permissionId).prop('checked', true);
            }


           });
          });
            checkIfUpdateCheckboxChecked();
       $("#pageloader").removeClass("pageloader");

           $("#updateRoleModal").modal('show');

      

                   },
     error: function (data) {
      
        

                
              }
  });


});

 $("#update-role").click(function(event){

  event.preventDefault();

  var data = $("#updateRole").serializeArray();

  let url  = site_url + "/updateRole";

  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "json",
    success: function(data) {
      $("#updateRoleModal").modal('hide');
          toastr.success(data.message);
        $('#roleTable').dataTable().fnClearTable();
      $('#roleTable').dataTable().fnDraw();
      $('#roleTable').dataTable().fnDestroy();
          roleTableList();

                   },
     error: function (data) {

      if(data.responseJSON.statusCode =="403"){
         $("#updateRoleModal").modal('hide');
          toastr.error(data.responseJSON.message);
      }
      var errors = data.responseJSON.errors;

       $.each(errors, function (key, value) 
          {
            $("#update_"+key).addClass("is-invalid");
            $(".update_"+key+"_error").html(value);
          

          });

        

                
              }
  });
});



})();

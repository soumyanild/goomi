$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/users/" + $(this).attr('data-id');
    let tableId = 'usersTable';
    deleteDataTableRecord(url, tableId);
});

$(document).ready(function() {
    console.log(site_url, '======site_url');
    $('#usersTable').DataTable({
        ajax: site_url + "/users/",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'status', name: 'status' },
            { data: 'document', name: 'document' },
            { data: 'created_at', name: 'created_at'},
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        ...defaultDatatableSettings
    });
});

$("#upload_file").change(function(event){
    $("#pageloader").addClass("pageloader");
    $("#import_users").submit();
});

// $("#import_users").submit(function(e){

//         e.preventDefault();
//             var formData = new FormData(this);

//     url = site_url + "/import-user";

//       $.ajax({
//     type: "POST",
//     url: url,
//     data:formData,
//         cache:false,
//         contentType: false,
//         processData: false,
//     success: function(response) {


      

//                    },
//      error: function (data) {
      
        

                
//               }
//   });


// });
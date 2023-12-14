function getEmailQueues(){	
    $('#emailQueueTable').DataTable({
        ajax: site_url + "/email-queue/",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'from_email', name: 'from_email' },
            { data: 'to_email', name: 'to_email' },
            { data: 'subject', name: 'subject' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at'},
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        ...defaultDatatableSettings
    });
}


$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/users/" + $(this).attr('data-id');
    let tableId = 'usersTable';
    deleteDataTableRecord(url, tableId);
});

$(document).ready(function() {
    getEmailQueues();
});
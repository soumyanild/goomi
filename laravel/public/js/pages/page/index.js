function getPages(){  
     $('#pageTable').DataTable({
        ajax: site_url + "/page/",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            { data: 'title', name: 'title' },
            { data: 'page_type', name: 'page_type' },
            { data: 'created_at', name: 'created_at'},
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        ...defaultDatatableSettings
    });
}


$(document).on('click', '.delete-datatable-record', function(e){
    let url  = site_url + "/page/" + $(this).attr('data-id');
    let tableId = 'pageTable';
    deleteDataTableRecord(url, tableId);
});

$(document).ready(function() {
    getPages();
});



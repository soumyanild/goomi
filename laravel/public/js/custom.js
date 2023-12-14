var site_url = window.location.protocol + '//' + window.location.host;
var toastCofig = {
  closeButton: true,
  tapToDismiss: false
}
var defaultDatatableSettings = {
  // responsive: true,
  language: {
    // search: "_INPUT_",
    searchPlaceholder: "Search",
  },
  processing: true,
  serverSide: true,
  lengthMenu: [ 10, 25, 50, 75, 100 ],
  pageLength: 25,
  // searchDelay: 500,
  // stateSave: true,
  order: [],
  footerCallback: function ( row, data, start, end, display ) {
    var api = this.api();

    // Remove the formatting to get integer data for summation
    var intVal = function ( i ) {
        return typeof i === 'string' ?
            i.replace(/[\$,]/g, '')*1 :
            typeof i === 'number' ?
                i : 0;
    };

    // Total over all pages
    total = api
        .column( 4 )
        .data()
        .reduce( function (a, b) {
            return intVal(a) + intVal(b);
        }, 0 );

    // Total over this page
    pageTotal = api
        .column( 4, { page: 'current'} )
        .data()
        .reduce( function (a, b) {
            return intVal(a) + intVal(b);
        }, 0 );

    // Update footer
    $( api.column( 4 ).footer() ).html(
        '$'+pageTotal +' ( $'+ total +' total)'
    );
  }
};

$(window).on('load', function() {
  if (feather) {
      feather.replace({
          width: 14,
          height: 14
      });
  }
});

$(".close").click(function(){
  $('.alert').fadeOut('slow');
});

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

function deleteDataTableRecord(url, tableId,className = ''){

  Swal.fire({
      title: "Are you sure want to delete this record?",
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary',
        cancelButton: 'btn btn-outline-danger ms-1'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $("#pageloader").addClass("pageloader");
          $.ajax({
            type: "DELETE",
            url: url,
            success: function (data) {
                $("#pageloader").removeClass("pageloader");
                if(data.statusCode >= 200 && data.statusCode < 400){
                    toastr.success(data.message);
                    if(className != ''){
                      $(className).remove();
                    }
                    let oTable = $('#'+tableId).dataTable(); 
                      oTable.fnDraw(false);
                }

                if(data.statusCode >= 400 && data.statusCode < 500){
                    toastr.info(data.message);
                }

                if(data.statusCode >= 500){
                  alert(data.message);
                    toastr.error(data.message);
                }
            },
            error: function (data) {
              $("#pageloader").removeClass("pageloader");
                if(data.responseJSON.statusCode >= 500){
                    toastr.error(data.responseJSON.message);
                }
            }
        });
      }
    });
}

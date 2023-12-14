@extends('layouts.admin')

@section('title') Dashboard @endsection

@section('content')
  <!-- Dashboard Ecommerce Starts -->
  <section id="dashboard-ecommerce">
    <div class="row match-height">

      <!-- Statistics Card -->
      <div class="col-xl-12 col-md-12 col-12">
          <div class="card card-statistics">
              <div class="card-header">
                  <h4 class="card-title">Statistics</h4>
                  <div class="d-flex align-items-center">
<!--                       <p class="card-text font-small-2 me-25 mb-0">Updated 1 month ago</p>
 -->                  </div>
              </div>
              <div class="card-body statistics-body">
                  <div class="row">
                      <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                        <a href="{{route('users.index')}}">
                          <div class="d-flex flex-row">
                              <div class="avatar bg-light-primary me-2">
                                  <div class="avatar-content">
                                      <i data-feather="user" class="avatar-icon"></i>
                                  </div>
                              </div>
                              <div class="my-auto">
                                  <h4 class="fw-bolder mb-0">{{$users}}</h4>
                                  <p class="card-text font-small-3 mb-0">Users</p>
                              </div>
                          </div>
                        </a>
                      </div>

              
                     
                     
                  </div>
              </div>
          </div>
      </div>
      <!--/ Statistics Card -->
    </div>


     <div class="row match-height">
                       
                        <!-- Revenue Report Card -->
                        <div class="col-lg-8 col-12">
                            <div class="card">
                                <div class="row mx-0">
                                    <div id="monthlyUsersRegistered"></div>

                                   
                                </div>
                            </div>
                        </div>
                        <!--/ Revenue Report Card -->


                         <div class="col-lg-4 col-12">
                            <div class="row match-height">
                                

                                <!-- Earnings Card -->
                                <div class="col-lg-12 col-md-6 col-12">
                                    <div class="card ">
                                        <div class="card-body">
                                      <div id="userPieChartData"></div>
                                           
                                        </div>
                                    </div>
                                </div>
                                <!--/ Earnings Card -->
                            </div>
                        </div>

                    </div>

  </section>
    <!-- Dashboard Ecommerce ends -->

@push('page_script')

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>

<script type="text/javascript">

   var usersJson = <?= $usersMonthlyData?>;

  var usersJson = JSON.parse(JSON.stringify(usersJson));

  console.log(usersJson);

  var users = [];

  $.each(usersJson, function(index, value) {
    users.push(value);

  });

  console.log(users);

   var categoriesJson = <?= $categories?>;

  var categoriesJson = JSON.parse(JSON.stringify(categoriesJson));

  var categories = [];

  $.each(categoriesJson, function(index, value) {
    categories.push(value);

  });

  Highcharts.setOptions([]);
 new Highcharts.chart('monthlyUsersRegistered',
  {"chart":{
    "renderTo":"monthlyUsersRegistered",
    "type":"column"
  },
  "credits":{
    "enabled":false
  },
   navigation: {
    buttonOptions: {
    enabled: false
    }
   },
  "title":{
    "text":"Monthly Users Registered"
  },
  "xAxis":{
    "categories":categories},
    "yAxis":{
      "title":{
        "text":"Count"}},
        "series":[{
          "name":"Total Users",
          "data":users,
          "color":"#7367f0"
        }
 ]});

 var data = <?= $userPieChartData?>;

  // Build the chart
Highcharts.chart('userPieChartData', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
   
  title: {
    text: 'Statistics'
  },
   credits: {
    enabled: false
},
  navigation: {
    buttonOptions: {
    enabled: false
    }
   },
 
  accessibility: {
    point: {
      valueSuffix: '%'
    }
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      dataLabels: {
        enabled: true
      },
      showInLegend: true
    }
  },
  series: [{
    name: 'Total Count ',

    colorByPoint: true,
    data: data,
  }]
});
</script>

@endpush

    
@endsection

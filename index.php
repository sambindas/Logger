<?php

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();

if ($_SESSION['logged_user'] == 'client') {
    header('Location: clientindex.php');
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$noww = date('M Y');
if (isset($_POST['s_date'])) {
    $from = $_POST['from'];
    $to = $_POST['to'];

    $fromm = date('d-M-Y' , strtotime($from));
    $too = date('d-M-Y' , strtotime($to));

$il = mysqli_query($conn, "SELECT * from issue where fissue_date between '$from' and '$to'");

    $noww = $fromm.' to '.$too;
} else {
$il = mysqli_query($conn, "SELECT * from issue where month = '$noww' order by issue_id desc");
}

$incident_logger = mysqli_query($conn, "SELECT * from user where status = 1 and user_type = 0 and state_id = $user_state_id");
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content = "7200; url=logout.php">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>eClat Healthcare Incident Tracker</title>
    <!-- Material Design Bootstrap -->
    <link href="assets/css/mdb.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6/css/select2.min.css" rel="stylesheet"/>
    <!-- amcharts css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" media="screen" href="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.gallery.min.css" />

    <style type="text/css">
        a.fancybox img {
            border: none;
            height: 50;
            width: 50;
            box-shadow: 0 1px 7px rgba(0,0,0,0.6);
            -o-transform: scale(1,1); -ms-transform: scale(1,1); -moz-transform: scale(1,1); -webkit-transform: scale(1,1); transform: scale(1,1); -o-transition: all 0.2s ease-in-out; -ms-transition: all 0.2s ease-in-out; -moz-transition: all 0.2s ease-in-out; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out;
        } 
        a.fancybox:hover img {
            position: relative; z-index: 999; -o-transform: scale(1.03,1.03); -ms-transform: scale(1.03,1.03); -moz-transform: scale(1.03,1.03); -webkit-transform: scale(1.03,1.03); transform: scale(1.03,1.03);
        }
        .dropdown-item:hover {
            background-color: #6394e2;
        }
    </style>
    <style type="text/css">
        .modal.and.carousel {
  position: fixed; //
}
    </style>
    <link rel="stylesheet" href="jquery.datetimepicker.css">
<script type="text/javascript" src="assets/ckeditor/ckeditor.js"></script>
    <!-- modernizr css -->
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body id="mybody">
            <?php
            require 'sidebar.php';
            require 'header.php';
            ?>
            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Dashboard</h4><br><br>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.php">Home</a></li>
                                <li><span>Incident Log</span></li>
                                <li><span></span></li>
                                <li><span></span></li>
                                <a href="new.php" id="newissue" class="btn btn-primary btn-flat">New Incident</a>
                                <li>
                                <?php 
                                if (isset($_SESSION['msg'])) {
                                    echo $_SESSION['msg'];
                                    unset($_SESSION['msg']);
                                }
                                ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown"><?php echo $_SESSION['name']; ?> <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a data-toggle='modal' data-target="#switch" class="dropdown-item">Switch States</a>
                                <a class="dropdown-item" href="settings.php">Settings</a>
                                <a class="dropdown-item" href="changepassword.php">Change Password</a>
                                <a class="dropdown-item" href="help.php">Help</a>
                                <a class="dropdown-item" href="logout.php">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='modal fade' id='switch'>
                <div class='modal-dialog modal-notify modal-primary modal-notify modal-primary modal-sm'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Switch States</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Currently Viewing <?php echo '<b>'.$state_name.'</b>'; ?></p><br><br>
                            <form method="post" action="processing.php">
                                <select name="state" class="custom-select border-0 pr-3" required>
                                    <option value="" selected="">Select State</option>
                                    <?php
                                    $fc = mysqli_query($conn, "SELECT * from state where id != $user_state_id order by state_name asc");
                                    while ($fc_row = mysqli_fetch_array($fc)) {
                                        echo '<option value="'.$fc_row['id'].'">'.$fc_row['state_name'].'</option>';
                                    }
                                    ?>
                                </select>
                                <br><button type='submit' class='btn btn-primary' name='submit_switch'>Switch</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- Primary table start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">
                                    <?php

                                        if ($_SESSION['id'] == 1 or $_SESSION['id'] == 6) {
                                            echo '<b>ALL</b>'; ?> Incidents for <?php echo $noww; 
                                        } else {
                                            echo '<b>'.$state_name.'</b>'; ?> Incident Log for <?php echo $noww; 
                                        }
                                        
                                    ?>
                                </h4>
                                <hr>
                                <form action="javascript:;" id="filterid">

                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label for="ex1">From</label>
                                        <input type="text" class="form-control" id="datetimepicker1" value="<?php echo date('Y-m-01') ?>" readonly placeholder="From" name="from"><i class="ti-calender"></i>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="ex1">To</label>
                                        <input type="text" class="form-control" id="datetimepicker2" value="<?php echo date('Y-m-d') ?>" readonly placeholder="From" name="to"><i class="ti-calender"></i><br>
                                    </div> &nbsp;
                                    <div class="col-sm-1">
                                        <label>Assigned</label>
                                        <select class="form-control" id="filter_assign">
                                            <option value="">All</option>
                                            <option value="<?php echo $_SESSION['name']; ?>">Me</option>
                                        </select>
                                    </div> &nbsp;&nbsp; &nbsp;&nbsp;
                                    <div class="col-sm-2">
                                        <label>Incident Status</label>
                                        <select class="form-control" id="filter_status">
                                            <option value="">Select One</option>
                                            <option value="0">Open / Unassigned</option>
                                            <option value="8">Open / Assigned</option>
                                            <option value="1">Done</option>
                                            <option value="3">Confirmed</option>
                                            <option value="2">Not An Issue</option>
                                            <option value="5">Not Clear</option>
                                            <option value="6">Approval Required</option>
                                            <option value="7">Not Approved</option>
                                            <option value="4">Incomplete</option>
                                            <option value="9">Incomplete Information</option>
                                        </select>
                                    </div>&nbsp;&nbsp;
                                    <div class="col-sm-2">
                                        <label>Incident Logger</label>
                                        <select class="form-control" id="logger">
                                            <option value="">Select One</option>
                                            <?php
                                            while ($logger = mysqli_fetch_array($incident_logger)) {
                                                echo '<option value="'.$logger['user_id'].'">'.$logger['user_name'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>&nbsp;&nbsp;
                                    <div class="col-sm-2">
                                        <label>View</label>
                                        <select class="form-control" id="view">
                                            <option value="0">Support</option>
                                            <option value="1">Client</option>
                                            ?>
                                        </select>
                                    </div>&nbsp;&nbsp;
                                    <div class="col-sm-2">
                                    <label>&nbsp;&nbsp;</label><br>
                                        <input type="submit" name="filter" id="filter" class="btn-flat btn btn-primary btn-xs" value="Filter">
                                    </div>
                                </div>


                                <!-- <div class="form-group" style="margin-bottom:20px">
                                    <div class="col-sm-12">
                                        <div class="col-sm-6">
                                            <span class="label" style="background:#93f575 !important;color:#158703">Confirmed</span>
                                            <span class="label" style="background:#f8fab3 !important;color:#158703">Awaiting attention</span>
                                            <span class="label" style="background:#faafaf !important;color:#158703">Urgent</span>
                                            <span class="label" style="background:#B9B9F9 !important;color:#158703">Booked</span>
                                        </div>
                                    </div>
                                </div>  -->
                                <hr>
                                
                                </form><br>

                                <div class="data-tables datatable-primary">

                                    <div class="col-xs-12" style="float: right">
                                        <form>
                                            <input type="text" name="search_table" id="search_table" class="form-control" placeholder="Search">
                                        </form>
                                    </div>
                                    <div id="my_table">
                                        <table id="dataTable2" class="text-center cell-border">
                                            <thead class="text-capitalize">
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Facility</th>
                                                    <th>Type</th>
                                                    <th align="right">Incident</th>
                                                    <th>Priority</th>
                                                    <th>Submitted By</th>
                                                    <th>Date Logged</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Primary table end -->
                </div>
            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <footer>
            <div class="footer-area">
                <p>Copyright <?php echo date('Y'); ?>. All right reserved.</p>
            </div>
        </footer>
        <!-- footer area end-->
    </div>
    <!-- page container area end -->
    <!-- jquery latest version -->
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function(){
            jQuery('#datetimepicker2').datetimepicker({
                format: 'Y-m-d',
                timepicker:false,
                maxDate: '0d',
            });

            jQuery('#datetimepicker1').datetimepicker({
             i18n:{
              de:{
               months:[
                'January','February','March','April',
                'May','June','July','August',
                'September','October','November','December',
               ],
               dayOfWeek:[
                "Su.", "Mo", "Tu", "We", 
                "Th", "Fr", "Sa.",
               ]
              }
             },
             format:'Y-m-d',
             timepicker:false,
             maxDate: '0d',
            });
        });
    </script>

    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

    <script type="text/javascript" language="javascript" >
     $(document).ready(function(){
      $.fn.dataTable.ext.errMode = 'none';
        fill_datatable();
      
      
      function fill_datatable(filter_status = '', filter_assign = '', logger = '', view = 0, datetimepicker1 = '', datetimepicker2 = '', search_table = '')
      {
       var dataTable = $('#dataTable2').DataTable({
        
        "processing" : true,
        "pageLength": 25,
        "columnDefs": [
            { "searchable": true, "targets": 0 }
          ],
        "serverSide" : true,
        "createdRow": function(row, data, index) {

            switch (data[8]) {
                case '0': 
                    $(row).css('background-color', 'white');
                    break;
                case '1':
                    $(row).css('background-color', '#f49b42');
                    break;
                case '2': 
                    $(row).css('background-color', '#7d998b');
                    break;
                case '3':
                    $(row).css('background-color', '#42f45f');
                    break;
                case '4': 
                    $(row).css('background-color', '#f4e624');
                    break;
                case '5':
                    $(row).css('background-color', '#5394ed');
                    break;
                case '6': 
                    $(row).css('background-color', '#42ebf4');
                    break;
                case '7':
                    $(row).css('background-color', '#f95454');
                    break;
                case '8': 
                    $(row).css('background-color', '#f6f6ad');
                    break;
                case '9': 
                    $(row).css('background-color', '#e777e3');
                    break;
                default:
                    $(row).css('background-color', 'white');
            }
        },
        "order" : [],
        "searching" : false,
        "ajax" : {
         url:"ajax/fetch.php",
         type:"POST",
         data:{
          filter_status:filter_status, logger:logger, view:view, filter_assign:filter_assign, datetimepicker1:datetimepicker1, datetimepicker2:datetimepicker2, search_table:search_table
         }
        },
        "columnDefs": [
            { "width": "40%", "targets": 3,
            "className": "text-justify", "targets": 3,
            "searchable": true, "targets": 3,
             }
          ]
       });
      }
      $(document).on( 'keyup', '#search_table', function () {
        var filter_status = $('#filter_status').val();
        var filter_assign = $('#filter_assign').val();
        var search_table = $('#search_table').val();
        var datetimepicker1 = '';
        var datetimepicker2 = '';
        var logger = $('#logger').val();
        var view = $('#view').val();
        
        if(search_table != '')
           {
            $('#dataTable2').DataTable().destroy();
            fill_datatable(filter_status, filter_assign, logger, view, datetimepicker1, datetimepicker2, search_table);
           }
           else
           {
            $('#dataTable2').DataTable().destroy();
            fill_datatable(filter_status, filter_assign); }
        } );
      $(document).on("change", "#filter_assign", function(){
       var filter_status = $('#filter_status').val();
       var filter_assign = $('#filter_assign').val();
       var logger = $('#logger').val();
       var view = $('#view').val();

       if(filter_status != '' || filter_assign != '' || logger !='' || view != '')
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable(filter_status, filter_assign, logger, view);
       }
       else
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable();
       }
      });

      $(document).on("change", "#logger", function(){
       var filter_status = $('#filter_status').val();
       var filter_assign = $('#filter_assign').val();
       var logger = $('#logger').val();
       var view = $('#view').val();

       if(filter_status != '' || filter_assign != '' || logger !='' || view !='')
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable(filter_status, filter_assign, logger, view);
       }
       else
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable();
       }
      });

      $(document).on("change", "#view", function(){
       var filter_status = $('#filter_status').val();
       var filter_assign = $('#filter_assign').val();
       var logger = $('#logger').val();
       var view = $('#view').val();

       if(filter_status != '' || filter_assign != '' || logger !='' || view != '')
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable(filter_status, filter_assign, logger, view);
       }
       else
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable();
       }
      });

      $(document).on("change", "#filter_status", function(){
       var filter_status = $('#filter_status').val();
       var filter_assign = $('#filter_assign').val();
       var logger = $('#logger').val();
       var view = $('#view').val();

       if(filter_status != '' || filter_assign != '' || logger !='' || view !='')
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable(filter_status, filter_assign, logger, view);
       }
       else
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable();
       }
      });

      $(document).on("click", "#filter", function(){
       var filter_status = $('#filter_status').val();
       var filter_assign = $('#filter_assign').val();
       var datetimepicker1 = $('#datetimepicker1').val();
       var datetimepicker2 = $('#datetimepicker2').val();
       var logger = $('#logger').val();
       var view = $('#view').val();

       if(filter_status != '' || filter_assign != '' || datetimepicker1 != '')
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable(filter_status, filter_assign, logger, view, datetimepicker1, datetimepicker2);
       }
       else
       {
        $('#dataTable2').DataTable().destroy();
        fill_datatable();
       }
      });
      
      
     });
     
    </script>
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.min.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.gallery.min.js"></script>
    
    <script>
    $.featherlightGallery.prototype.afterContent = function () {
        var caption = this.$currentTarget.find('img').attr('alt');
        this.$instance.find('.caption').remove();
        $('<div class="caption">').text(caption).appendTo(this.$instance.find('.featherlight-content'));
    }
</script>
    <script src="jquery.datetimepicker.full.min.js"></script>
    <script src="jquery.datetimepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
                dropdownParent: $('.js-example-basic-multiple')
        });
    </script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="assets/js/mdb.min.js"></script>

</html>
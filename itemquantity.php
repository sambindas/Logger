<?php

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();
if ($_SESSION['logged_user'] == 'client') {
    header('Location: clientindex.php');
}
// error_reporting(E_ALL); 
// ini_set('display_errors', 1);

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (isset($_POST['submit_edt'])) {

    $sku = $_POST['sku'];
    $id = $_POST['id'];
    $item_name = $_POST['item_name'];
    $date = date('d-m-Y H:i:s');

    $query = mysqli_query($conn, "UPDATE items set item_name = '$item_name', updated_at = '$date' where sku = '$sku'");
    
    $_SESSION['msg'] = '<span class="alert alert-success">Item Edited Successfully.</span>';
}

if (isset($_POST['delete_f'])) {
    $id = $_POST['id'];

    mysqli_query($conn, "DELETE from items where id = $id");
    $_SESSION['msg'] = '<span class="alert alert-success">Item Deleted Successfully.</span>';
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    mysqli_query($conn, "DELETE from grn where grn_token = '$token'");
    $_SESSION['msg'] = '<span class="alert alert-success">GRN Deleted Successfully.</span>';
    header('Location: grn.php');
}

$location = mysqli_query($conn, "SELECT * from locations");

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Item Quantity</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
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
                    <div class="col-sm-8">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Dashboard</h4><br><br>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.php">Home</a></li>
                                <li><span>Inventory / </span></li>
                                <li><span>Item Quantities</span></li>
                                <li><span></span></li>
                                <li><span></span></li>
                                <?php 
                                if (isset($_SESSION['msg'])) {
                                    echo $_SESSION['msg'];
                                    unset($_SESSION['msg']);
                                }
                                ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown"><?php echo $_SESSION['name']; ?> <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="changepassword.php">Change Password</a>
                                <a class="dropdown-item" href="settings.php">Settings</a>
				                <a class="dropdown-item" href="help.php">Help</a>
                                <a class="dropdown-item" href="logout.php">Log Out</a>
                            </div>
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
                                <h4 class="header-title">Items Status</h4>                 
                                <form action="javascript:;" id="filterid">

                                    <div class="form-group row">
                                        <div class="col-sm-2">
                                            <label>Location</label>
                                            <select class="form-control js-example-basic-single" id="location">
                                                <option value="">Select One</option>
                                                <?php
                                                while ($logger = mysqli_fetch_array($location)) {
                                                    echo '<option value="'.$logger['code'].'">'.$logger['name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>&nbsp;&nbsp;
                                    </div>
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
                                                    <th>Location</th>
                                                    <th>Quantity</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Primary table end -->
                    <div class="modal fade" id="yes" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Transfer Details</h4>
                                </div>
                                <div class="modal-body">
                                
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <footer>
            <div class="footer-area">
                <p>© Copyright 2019. All right reserved.</p>
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

    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="jquery.datetimepicker.full.min.js"></script>
    <script src="jquery.datetimepicker.js"></script>
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

    <script type="text/javascript" language="javascript" >
         $(document).ready(function(){
          $.fn.dataTable.ext.errMode = 'none';
            fill_datatable();
          
        
          function fill_datatable(location = '', search_table = '')
          {
           $('#dataTable2').DataTable({
            
            "processing" : true,
            "pageLength": 25,
            "columnDefs": [
                { "searchable": true, "targets": 0 }
              ],
            "serverSide" : true,
            "order" : [],
            "searching" : false,
            "ajax" : {
             url:"ajax/quantity.php",
             type:"POST",
             data:{location:location, search_table:search_table
             }
            }
           });
          }
        $(document).on( 'keyup', '#search_table', function () {
        var search_table = $('#search_table').val();
        var location = $('#location').val();
        
        if(search_table != '')
           {
            $('#dataTable2').DataTable().destroy();
            fill_datatable(location, search_table);
           }
           else
           {
            $('#dataTable2').DataTable().destroy();
            fill_datatable(); }
        } );
        $(document).on("change", "#location", function(){
        var location = $('#location').val();
        var search_table = $('#search_table').val();
        console.log(search_table);
        if(location != '')
        {
            $('#dataTable2').DataTable().destroy();
            fill_datatable(location, search_table);
         }
         else
         {
            $('#dataTable2').DataTable().destroy();
            fill_datatable();
         }
         });
        });
         
    </script>
    <script type="text/javascript">
        function viewItems(token) {
            var token = String(token);
            console.log(token)
            $.ajax({
            url: 'ajax/transferr.php',
            type: 'post',
            data: {token: token},
            success: function(response){ 
                // Add response in Modal body
                $('.modal-body').html(response);

                // Display Modal
                $('#yes').modal('show'); 
            }
            });
        }
    </script>
    <script type="text/javascript">
        var dataTable = $('#dataTable22').DataTable({});
    </script>

</html>
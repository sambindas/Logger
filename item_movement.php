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
    header('Location: transfer.php');
}

if (isset($_POST['submit_media'])) {
    if (isset($_FILES['media'])) {

        $prefix = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 7);
        $dir = 'media/grn/';
        $url = "transfer.php";
        $so = $_SESSION['id'];
        $grn_token = $_POST['grn_token'];


        $fileName = $prefix.$_FILES['media']['name'];
        $file_size = $_FILES['media']['size'];
        $file_tmp = $_FILES['media']['tmp_name'];
        $file_type= $_FILES['media']['type'];
        $filePath = $dir.$fileName;

        if ($file_size > 10000000) {
            $_SESSION['msg'] = '<span class="alert alert-danger">File Size Must Be Lower Than 10mb</span>';
            header("Location: $url");
            return false;
        }

        if ($file_type != 'image/png' && $file_type != 'image/jpg' && $file_type != 'image/jpeg' && $file_type != 'image/gif' && $file_type != 'application/pdf') {
            $_SESSION['msg'] = '<span class="alert alert-danger">File Must Be Either Pdf, Jpg, Png or Gif</span>';
            header("Location: $url");
            return false;
        }

        if (move_uploaded_file($file_tmp, $filePath)) {

        $query_image = "INSERT INTO grn_document (file_name, grn_token, created_by, created_at) VALUES ('$fileName','$grn_token','$so', now())";

        if (mysqli_query($conn, $query_image)) {
            $_SESSION['msg'] = '<span class="alert alert-danger">Document Uploaded Successfully</span>';
            header("Location: $url");
        }
        
        }

    }
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Inventory GRN/Issue</title>
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
    <link rel="stylesheet" type="text/css" media="screen" href="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.gallery.min.css" />
    <!-- style css -->
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/lightbox.css">
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
                                <li><span>GRN/Issue</span></li>
                                <li><span></span></li>
                                <li><span></span></li>
                                <a href="grn.php" id="newissue" class="btn btn-primary btn-flat">New GRN</a>
                                <a href="issue.php" id="newissue" class="btn btn-primary btn-flat">Issue Item</a>
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
                                <h4 class="header-title">Items Transfer</h4>                 
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
                                        <!-- <div class="col-sm-2">
                                            <label>Incident Status</label>
                                            <select class="form-control js-example-basic-single" id="filter_status">
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
                                                <option value="10">Not Applicable</option>
                                            </select>
                                        </div>&nbsp;&nbsp;
                                        <div class="col-sm-2">
                                            <label>Incident Logger</label>
                                            <select class="form-control js-example-basic-single" id="logger">
                                                <option value="">Select One</option>
                                                <?php
                                                while ($logger = mysqli_fetch_array($incident_logger)) {
                                                    echo '<option value="'.$logger['user_id'].'">'.$logger['user_name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>&nbsp;&nbsp;
                                        <div class="col-sm-2">
                                            <label>Facility</label>
                                            <select class="form-control js-example-basic-single" id="facility">
                                                <option value="">All</option>
                                                <?php
                                                while ($facc = mysqli_fetch_array($facility)) {
                                                    echo '<option value="'.$facc['code'].'">'.$facc['name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>&nbsp;&nbsp; -->
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
                                                    <th>From</th>
                                                    <th>To</th>
                                                    <th>Type</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
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

    <script type="text/javascript" src="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.min.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/noelboss/featherlight/1.7.13/release/featherlight.gallery.min.js"></script>
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="jquery.datetimepicker.full.min.js"></script>
    <script src="jquery.datetimepicker.js"></script>
    <script src="assets/js/lightbox.js"></script>
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
          
        
          function fill_datatable(datetimepicker1 = '', datetimepicker2 = '', search_table = '')
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
             url:"ajax/items_movement.php",
             type:"POST",
             data:{datetimepicker1:datetimepicker1, datetimepicker2:datetimepicker2, search_table:search_table
             }
            }
           });
          }
        $(document).on("click", "#filter", function(){
        var datetimepicker1 = $('#datetimepicker1').val();
        var datetimepicker2 = $('#datetimepicker2').val();
        
        if(datetimepicker1 != '')
        {
            $('#dataTable2').DataTable().destroy();
            fill_datatable(datetimepicker1, datetimepicker2);
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
            $(document).ready(function(){
                
                $('#form_submit').click(function(){

                    var sku = $('#sku').val();
                    var item_name = $('#item_name').val();

                    if (sku == '' || item_name == '') {
                        $('#formErr').html('<span class="alert alert-danger">Please Fill Required Fields</span>');
                        return false;
                    }
                    else if (sku != '') {
                        $('#formErr').html('');

                        var datastring = 'sku='+sku;

                        $.ajax({
                            url: 'ajax/items.php',
                            method: 'post',
                            data: datastring,
                            success: function(msg) {
                                if (msg == 1) {
                                    $('#errsku').html('<div class="alert alert-danger"><p>SKU Already Exists</p></div>');

                                    return false;

                                } else {
                                    $('#errem').html('');
                                    registerFinal();
                                }
                            }
                        });

                        var datastringg = 'item_name='+item_name+'&sku='+sku;

                        function registerFinal() {

                        $.ajax({
                            url: 'ajax/items.php',
                            method: 'post',
                            data: datastringg,
                            success: function(msg) {
                                if (msg == 1) {
                                    window.location.replace('item.php');
                                }else {
                                    $('#loaderxy').html('<span class="alert alert-danger">Something Went wrong. Please try again</span>');
                                }
                            }
                        });
                    }
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
    <script type="text/javascript">

        function readURL(input) {

            if (input.files && input.files[0]) {

                var reader = new FileReader();

                

                reader.onload = function (e) {

                    $('#profile-img-tag').attr('src', e.target.result);

                }

                reader.readAsDataURL(input.files[0]);

            }

        }

        $("#profile-img").change(function(){

            readURL(this);

        });

    </script>
</html>
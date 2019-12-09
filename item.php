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

    $query = mysqli_query($conn, "UPDATE items set item_name = '$item_name', updated_at = now() where sku = '$sku'");
    
    $_SESSION['msg'] = '<span class="alert alert-success">Item Edited Successfully.</span>';
}

if (isset($_POST['submit_grn'])) {

    $created_by = $_SESSION['id'];
    $pin = mt_rand(100000000, 999999999)
        . mt_rand(100000000, 999999999)
        . $characters[rand(0, strlen($characters) - 1)];
    $r_token = 'rec-'.str_shuffle($pin);

    $location = $_POST['location'];
    $item = $_POST['item'];
    $sku = $_POST['sku'];
    $quantity = $_POST['transfer_qty'];
    $received_from = $_POST['received_from'];

    $t = mysqli_query($conn, "SELECT * from locations where name = '".$location."'");
    while ($fr = mysqli_fetch_array($t)) {
        $code = $fr['code'];
    }

    $sql = "INSERT INTO receive_grn(receive_token, item_name, quantity_received, created_at, created_by, type, tfrom, tto)
                VALUES('$r_token', '$item', '$quantity', now(), '$created_by', 0, '$received_from', '$location')";
        
        if(mysqli_query($conn, $sql)){
            
            $location_from = mysqli_query($conn, "SELECT * from items where sku = '$sku'");
            
                #add quantity to location sending to
                while ($location_from_d = mysqli_fetch_array($location_from)) {

                    $location_from_db = json_decode($location_from_d['item_quantity'], true);
                    if (array_key_exists($code, $location_from_db)) {
                        foreach ($location_from_db as $key => $value) {
                            if ($code == $key) {
                                $new_value = $value + $quantity;
                                $location_from_db[$key] = $new_value;
                                $updated_array = json_encode($location_from_db);
                                $u = mysqli_query($conn, "UPDATE items set item_quantity = '$updated_array' where sku = '$sku'");
                            }
                        }
                    }
                }
        $_SESSION['msg'] = '<span class="alert alert-success">Item Quantity Balanced Successfully.</span>';
        header('Location: item.php');
        exit();
    }
}

if (isset($_POST['submit_issue'])) {

    $created_by = $_SESSION['id'];
    $pin = mt_rand(100000000, 999999999)
        . mt_rand(100000000, 999999999)
        . $characters[rand(0, strlen($characters) - 1)];
    $r_token = 'rec-'.str_shuffle($pin);

    $location = $_POST['location'];
    $item = $_POST['item'];
    $sku = $_POST['sku'];
    $quantity = $_POST['transfer_qty'];
    $transfer_to = $_POST['transfer_to'];

    $t = mysqli_query($conn, "SELECT * from locations where name = '".$location."'");
    while ($fr = mysqli_fetch_array($t)) {
        $code = $fr['code'];
    }

    $sql = "INSERT INTO receive_grn(receive_token, item_name, quantity_received, created_at, created_by, type, tfrom, tto)
                VALUES('$r_token', '$item', '$quantity', now(), '$created_by', 0, '$location', '$transfer_to')";

        if(mysqli_query($conn, $sql)){
            $location_from = mysqli_query($conn, "SELECT * from items where sku = '$sku'");
                #add quantity to location sending to
                while ($location_from_d = mysqli_fetch_array($location_from)) {

                    $location_from_db = json_decode($location_from_d['item_quantity'], true);  
                    if (array_key_exists($code, $location_from_db)) {
                        foreach ($location_from_db as $key => $value) {
                            if ($code == $key) {
                                $new_value = $value - $quantity;
                                $location_from_db[$key] = $new_value;
                                $updated_array = json_encode($location_from_db);
                                $u = mysqli_query($conn, "UPDATE items set item_quantity = '$updated_array' where sku = '$sku'");
                            }
                        }
                    }
                }
        $_SESSION['msg'] = '<span class="alert alert-success">Item Quantity Balanced Successfully.</span>';
        header('Location: item.php');
        exit();
    }
}

if (isset($_POST['delete_f'])) {
    $id = $_POST['id'];

    mysqli_query($conn, "DELETE from items where id = $id");
    $_SESSION['msg'] = '<span class="alert alert-success">Item Deleted Successfully.</span>';
}

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Items</title>
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
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Dashboard</h4><br><br>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.php">Home</a></li>
                                <li><span>Inventory / </span></li>
                                <li><span>Items</span></li>
                                <li><span></span></li>
                                <li><span></span></li>
                                <li><button id="newissue" class="btn btn-primary btn-flat" data-toggle="modal" data-target=".newissue">Add New</button></li>
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
                                <h4 class="header-title">Inventory Items</h4>
                                <div class="data-tables datatable-primary">
                                    <div id="my_table">
                                        <table id="dataTable22" class="text-center table table-hover">
                                            <thead class="text-capitalize">
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>SKU</th>
                                                    <th>Item Name</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $il = mysqli_query($conn, "SELECT * from items");
                                                $sn = 1;
                                                while ($li_row = mysqli_fetch_array($il)) {
                              
                                                ?>
                                                <tr>
                                                    <td><?php echo $sn++?></td>
                                                    <td><?php echo $li_row['sku'] ; ?></td>
                                                    <td><?php echo $li_row['item_name'] ; ?></td>
                                                    <td><div class="dropdown">
                                                            <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <?php
                                                                    echo '<a data-toggle="modal" data-target="#grn'.$li_row["id"].'" class="dropdown-item" href="#">Create GRN</a>';
                                                                    echo '<a data-toggle="modal" data-target="#issue'.$li_row["id"].'" class="dropdown-item" href="#">Issue Item</a>';

                                                                    echo '<a data-toggle="modal" data-target="#edt'.$li_row["id"].'" class="dropdown-item" href="#">Edit</a>';
                                                                    echo '<a data-toggle="modal" data-target="#del'.$li_row["id"].'" class="dropdown-item" href="#">Delete</a>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            <!-- edit modal start -->
                                            <div class="modal fade" id="edt<?php echo $li_row['id']; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Item</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- login area start -->
                                                            <div class="login-area">
                                                                <div class="container">
                                                                    <form action="" method="post">
                                                                        <div class="login-form-body">
                                                                            <div class="form-gp">
                                                                                <input type="text" name="sku" value="<?php echo $li_row['sku']; ?>" readonly="true" required>
                                                                                <i class="ti-user"></i><br>
                                                                                <div id="errsku"></div>
                                                                            </div>
                                                                            <div class="form-gp">
                                                                                <input type="text" value="<?php echo $li_row['item_name']; ?>" name="item_name" required>
                                                                                <i class="ti-user"></i><br>
                                                                                <div id="errin"></div>
                                                                            </div>
                                                                            <div class="submit-btn-area">
                                                                                <input class="btn btn-primary" id="submit_edt" name="submit_edt" type="submit" value="Submit">
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- login area end -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- issue item modal start -->
                                            <div class="modal fade" id="issue<?php echo $li_row['id']; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Issue Item</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            *This will deduct from this item's inventory quantity
                                                            <!-- login area start -->
                                                            <div class="login-area">
                                                                <div class="container">
                                                                    <form action="" method="post">
                                                                        <div class="login-form-body">
                                                                            <div class="form-gp">
                                                                                <select name="location" id="location" class="cus custom-select border-0 pr-3" required>
                                                                                    <option value="" selected="">Select Location</option>
                                                                                    <?php
                                                                                    $fc = mysqli_query($conn, "SELECT * from locations");
                                                                                    while ($fc_row = mysqli_fetch_array($fc)) {
                                                                                        echo '<option value="'.$fc_row['name'].'">'.$fc_row['name'].'</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                            <input type="hidden" value="<?php echo $li_row['item_name']; ?>" name="item">
                                                                            <input type="hidden" value="<?php echo $li_row['sku']; ?>" name="sku">
                                                                            <div class="form-gp">
                                                                                <label for="validationCustom02">Transferred To</label>
                                                                                <input type="text" placeholder="Transferred To" name="transfer_to" required>
                                                                                <i class="ti-user"></i><br>
                                                                                <div id="errin"></div>
                                                                            </div>
                                                                            <div class="form-gp">
                                                                                <label for="validationCustom02">Quantity Transferred</label>
                                                                                <input type="text" placeholder="Quantity Transferred" name="transfer_qty" required>
                                                                                <i class="ti-user"></i><br>
                                                                                <div id="errin"></div>
                                                                            </div>
                                                                            <div class="submit-btn-area">
                                                                                <input class="btn btn-primary" id="submit_issue" name="submit_issue" type="submit" value="Submit">
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- login area end -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- GRN modal start -->
                                            <div class="modal fade" id="grn<?php echo $li_row['id']; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Create GRN</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            *This will add to this item's inventory quantity
                                                            <!-- login area start -->
                                                            <div class="login-area">
                                                                <div class="container">
                                                                    <form action="" method="post">
                                                                        <div class="login-form-body">
                                                                            <div class="form-gp">
                                                                                <select name="location" id="location" class="cus custom-select border-0 pr-3" required>
                                                                                    <option value="" selected="">Select Location</option>
                                                                                    <?php
                                                                                    $fc = mysqli_query($conn, "SELECT * from locations");
                                                                                    while ($fc_row = mysqli_fetch_array($fc)) {
                                                                                        echo '<option value="'.$fc_row['name'].'">'.$fc_row['name'].'</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                            <input type="hidden" value="<?php echo $li_row['item_name']; ?>" name="item">
                                                                            <input type="hidden" value="<?php echo $li_row['sku']; ?>" name="sku">
                                                                            <div class="form-gp">
                                                                                <label for="validationCustom02">Received From</label>
                                                                                <input type="text" placeholder="Received From" name="received_from" required>
                                                                                <i class="ti-user"></i><br>
                                                                                <div id="errin"></div>
                                                                            </div>
                                                                            <div class="form-gp">
                                                                                <label for="validationCustom02">Quantity Received</label>
                                                                                <input type="text" placeholder="Quantity Transferred" name="transfer_qty" required>
                                                                                <i class="ti-user"></i><br>
                                                                                <div id="errin"></div>
                                                                            </div>
                                                                            <div class="submit-btn-area">
                                                                                <input class="btn btn-primary" id="submit_grn" name="submit_grn" type="submit" value="Submit">
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <!-- login area end -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- delete modal start -->
                                            <div class="modal fade" id="del<?php echo $li_row['id']; ?>">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Email</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are You Sure?</p>
                                                            <form method="post" action="">
                                                                <input type="hidden" name="id" value="<?php echo $li_row['id']; ?>">
                                                                <br><button type="submit" class="btn btn-primary" name="delete_f">Delete</button>
                                                            </form><br>
                                                        </div>
                                                        <div class="modal-footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Small modal modal end -->
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Primary table end -->

                    <!-- Large modal start -->
                    <!-- Large modal -->
                    <div class="newissue modal fade bd-example-modal-lg">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Item</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">

                                    <!-- login area start -->
                                    <div class="login-area">
                                        <div class="container">
                                            <form action="javascript:;" method="post">
                                                <div class="login-form-head">
                                                    <h4>Add Item</h4>
                                                    <p id="formErr"></p>
                                                </div>
                                                <div class="login-form-body">
                                                    <div class="form-gp">
                                                        <label for="exampleInputName1">SKU</label>
                                                        <input type="text" id="sku" name="sku" required>
                                                        <i class="ti-user"></i><br>
                                                        <div id="errsku"></div>
                                                    </div>
                                                    <div class="form-gp">
                                                        <label for="exampleInputName1">Item Name</label>
                                                        <input type="text" id="item_name" name="item_name" required>
                                                        <i class="ti-user"></i><br>
                                                        <div id="errin"></div>
                                                    </div>
                                                    <div class="submit-btn-area">
                                                        <input class="btn btn-primary" id="form_submit" name="form_submit" type="submit" value="Submit">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- login area end -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Large modal modal end -->
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
        var dataTable = $('#dataTable22').DataTable({});
    </script>

</html>
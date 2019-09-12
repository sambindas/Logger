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

if (isset($_POST['delete_f'])) {
    $id = $_POST['id'];

    mysqli_query($conn, "DELETE from user where user_id = $id");
    $_SESSION['msg'] = '<span class="alert alert-success">Credentials Deleted Successfully.</span>';
}

if (isset($_POST['edtp'])) {
    $id = $_POST['id'];
    $password = sha1($_POST['password']);

    mysqli_query($conn, "UPDATE user set password = '$password' where user_id = $id");
    $_SESSION['msg'] = '<span class="alert alert-success">Password Updated Successfully.</span>';
}

if (isset($_POST['submit_edt'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['fname']);
    $code = mysqli_real_escape_string($conn, $_POST['fcode']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    mysqli_query($conn, "UPDATE user set user_name = '$name', user_role = '$code', email = '$email', phone = '$phone' where user_id = $id");
    $_SESSION['msg'] = '<span class="alert alert-success">Credentials Updated Successfully.</span>';
}

$fac = mysqli_fetch_array(mysqli_query($conn, "SELECT * from facility"));
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Client Portal</title>
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
                                <li><span>Manage / </span></li>
                                <li><span>Client Portal</span></li>
                                <li><span></span></li>
                                <li><span></span></li>
                                <li><button id="newissue" class="btn btn-primary btn-flat" data-toggle="modal" data-target=".newissue">New Credential</button></li>
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
                                <h4 class="header-title">Login Credentials</h4>
                                <div class="data-tables datatable-primary">
                                    <div id="my_table">
                                        <table id="dataTable2" class="text-center table table-hover">
                                            <thead class="text-capitalize">
                                                <tr>
                                                    <th>Facility Name</th>
                                                    <th>Contact Person</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Action</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $il = mysqli_query($conn, "SELECT * from user where user_type = 1");
                                                $sn = 1;
                                                while ($li_row = mysqli_fetch_array($il)) {
                                                    $c = $li_row['user_role'];
                                                    $i = mysqli_fetch_array(mysqli_query($conn, "SELECT * from facility where code = '$c'"));
                                                ?>
                                                <tr>
                                                    <td><?php echo $i['name'] ; ?></td>
                                                    <td><?php echo $li_row['user_name'] ; ?></td>
                                                    <td><?php echo $li_row['email'] ; ?></td>
                                                    <td><?php echo $li_row['phone'] ; ?></td>
                                                    <td><div class="dropdown">
                                                            <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <?php
                                                                    echo '<a data-toggle="modal" data-target="#edt'.$li_row["user_id"].'" class="dropdown-item" href="#">Edit</a>';
                                                                    echo '<a data-toggle="modal" data-target="#pas'.$li_row["user_id"].'" class="dropdown-item" href="#">Change Password</a>';
                                                                    echo '<a data-toggle="modal" data-target="#del'.$li_row["user_id"].'" class="dropdown-item" href="#">Delete</a>';
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                 <!-- edit modal start -->
                                            <div class="modal fade" id="pas<?php echo $li_row['user_id']; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Credentials</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- login area start -->
                                                            <div class="login-area">
                                                                <div class="container">
                                                                        <form action="" method="post">
                                                                            <div class="login-form-head">
                                                                                <p id="formErr">Enter new password</p>
                                                                            </div>
                                                                            <div class="login-form-body">
                                                                                <div class="form-gp">
                                                                                    <input type="text" placeholder="Enter New Password" name="password" required>
                                                                                    
                                                                                    <div id="errfc"></div>
                                                                                </div>
                                                                                <input type="hidden" name="id" value="<?php echo $li_row['user_id']; ?>">
                                                                                <input type="hidden" name="url" value="<?php echo $url; ?>"><br>
                                                                                <div class="submit-btn-area">
                                                                                    <input class="btn btn-primary" name="edtp" type="submit" value="Submit">
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
                                            <!-- edit modal start -->
                                            <div class="modal fade" id="edt<?php echo $li_row['user_id']; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Credentials</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- login area start -->
                                                            <div class="login-area">
                                                                <div class="container">
                                                                        <form action="" method="post">
                                                                            <div class="login-form-head">
                                                                                <p id="formErr">Edit Credentials</p>
                                                                            </div>
                                                                            <div class="login-form-body">
                                                                                <div class="form-gp">
                                                                                    <input type="text" placeholder="Enter Facility Code" name="fcode" value="<?php echo $li_row['user_role']; ?>" required>
                                                                                    
                                                                                    <div id="errfc"></div>
                                                                                </div>
                                                                                <div class="form-gp">
                                                                                    <input type="text" name="fname" placeholder="Facility Name" value="<?php echo $li_row['user_name']; ?>" required>
                                                                                    
                                                                                    <div id="errfn"></div>
                                                                                </div>
                                                                                <div class="form-gp">
                                                                                    <input type="email" name="email" placeholder="Email" value="<?php echo $li_row['email']; ?>">
                                                                                    
                                                                                    <div id="errfn"></div>
                                                                                </div>
                                                                                <div class="form-gp">
                                                                                    <input type="text" name="phone" placeholder="Phone Number" value="<?php echo $li_row['phone']; ?>">
                                                                                    
                                                                                    <div id="errfn"></div>
                                                                                </div>
                                                                                <input type="hidden" name="id" value="<?php echo $li_row['user_id']; ?>">
                                                                                <input type="hidden" name="url" value="<?php echo $url; ?>"><br>
                                                                                <div class="submit-btn-area">
                                                                                    <input class="btn btn-primary" name="submit_edt" type="submit" value="Submit">
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
                                            <div class="modal fade" id="del<?php echo $li_row['user_id']; ?>">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Credentials</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are You Sure?</p>
                                                            <form method="post" action="">
                                                                <input type="hidden" name="id" value="<?php echo $li_row['user_id']; ?>">
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
                                    <h5 class="modal-title">Add Credentials</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">

                                    <!-- login area start -->
                                    <div class="login-area">
                                        <div class="container">
                                                <form action="javascript:;">
                                                    <div class="login-form-head">
                                                        <h4>Add Credentials</h4>
                                                        <p id="formErr"></p>
                                                    </div>
                                                    <div class="login-form-body">
                                                        <div class="form-gp">
                                                            <select id="scode" class="custome-select border-0 pr-3" required>
                                                                <option value="" selected="">Select State</option>
                                                                <?php
                                                                $fc = mysqli_query($conn, "SELECT * from state");
                                                                while ($fc_row = mysqli_fetch_array($fc)) {
                                                                    echo '<option value="'.$fc_row['id'].'">'.$fc_row['state_name'].'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <div id="errfc"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <select id="fcode" class="custome-select border-0 pr-3" required>
                                                                <option value="" selected="">Select State First</option>
                                                            </select>
                                                            <div id="errfc"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputName1">Contact Person</label>
                                                            <input type="text" id="fname" required>
                                                            <i class="ti-user"></i><br>
                                                            <div id="errfn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputName1">Email</label>
                                                            <input type="text" id="email" required>
                                                            <i class="ti-user"></i><br>
                                                            <div id="errfn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputName1">Phone</label>
                                                            <input type="text" id="phone" required>
                                                            <i class="ti-phone"></i><br>
                                                            <div id="errfn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputName1">Password</label>
                                                            <input type="text" id="password" required>
                                                            <i class="ti-user"></i><br>
                                                            <div id="errfn"></div>
                                                        </div>
                                                        <div class="submit-btn-area">
                                                            <input class="btn btn-primary" id="form_submit" type="submit" value="Submit">
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
        $('#scode').on('change',function(){
            var state = $(this).val();
            if(state){
                $.ajax({
                    type:'POST',
                    url:'ajax/ajaxData.php',
                    data:'state='+state,
                    success:function(html){
                        $('#fcode').html(html);
                    }
                }); 
            }else{
                $('#fcode').html('<option value="">Select State First</option>');
            }
        });
    })
    </script>
    <script type="text/javascript">
            $(document).ready(function(){
                
                $('#form_submit').click(function(){

                    var name = $('#fname').val();
                    var state = $('#scode').val();
                    var email = $('#email').val();
                    var password = $('#password').val();
                    var phone = $('#phone').val();
                    var code = $('#fcode').val();

                    console.log(code);


                    if (name == '' || state == '' || password == '' || email == '' || phone == '' || code == '') {
                        $('#formErr').html('<span class="alert alert-danger">Please Fill Required Fields</span>');
                        return false;
                    }
                    else if (name != '' || state != '' || password != '' || email != '' || phone != '' || code != '') {
                        $('#formErr').html('');

                        var datastring = 'email='+email;

                        $.ajax({
                            url: 'ajax/email.php',
                            method: 'post',
                            data: datastring,
                            success: function(msg) {
                                if (msg == 1) {
                                    $('#errfc').html('<div class="alert alert-danger"><p>Credential Exists</p></div>');

                                    return false;

                                } else {
                                    $('#errem').html('');
                                    registerFinal();
                                }
                            }
                        });

                        var datastringg = 'name='+name+'&state='+state+'&password='+password+'&email='+email+'&phone='+phone+'&code='+code;

                        function registerFinal() {

                        $.ajax({
                            url: 'ajax/cfac.php',
                            method: 'post',
                            data: datastringg,
                            success: function(msg) {
                                if (msg == 1) {
                                    window.location.replace('portal.php');
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
        var dataTable = $('#dataTable2').DataTable({});
    </script>

</html>
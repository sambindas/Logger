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

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Users</title>
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
                            <h4 class="page-title pull-left">Dashboard</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.php">Home</a></li>
                                <li><span>Manage / </span></li>
                                <li><span>Users</span></li>
                                <li><span></span></li>
                                <li><span></span></li>
                                <li><button id="newissue" class="btn btn-primary btn-flat" data-toggle="modal" data-target=".newissue">New User</button></li>
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
                                <a class="dropdown-item" href="settings.php">Settings</a>
                                <a class="dropdown-item" href="changepassword.php">Change Password</a>
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
                                <h4 class="header-title">Users</h4>
                                <div class="data-tables datatable-primary">
                                    <div id="my_table">
                                        <table id="dataTable2" class="text-center table table-hover">
                                            <thead class="text-capitalize">
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Name</th>
                                                    <th>Role</th>
                                                    <th>State</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Actions</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $il = mysqli_query($conn, "SELECT * from user");
                                                $sn = 1;
                                                while ($li_row = mysqli_fetch_array($il)) {
                                                $state_id = $li_row['state_id'];
                                                $l = mysqli_query($conn, "SELECT * from state where id='$state_id'");
                                                while ($l_row = mysqli_fetch_array($l)) { $state_name = $l_row['state_name']; }
                                                ?>
                                                <tr>
                                                    <td><?php echo $sn++; ?></td>
                                                    <td><?php echo $li_row['user_name'] ; ?></td>
                                                    <td><?php echo $li_row['user_role'] ; ?></td>
                                                    <td><?php echo $state_name; ?></td>
                                                    <td><?php echo $li_row['email'] ; ?></td>
                                                    <td><?php echo $li_row['phone'] ; ?></td>
                                                    <td><div class="dropdown">
                                                            <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <?php
                                                                if ($li_row['status']==0) {
                                                                    echo '<a class="dropdown-item" data-toggle="modal" data-target="#act'.$li_row["user_id"].'" href="#">Activate</a>';
                                                                } else {
                                                                    echo '<a data-toggle="modal" data-target="#dact'.$li_row["user_id"].'" class="dropdown-item" href="#">Deactivate</a>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>

                                            <!-- deactivate modal start -->
                                            <div class="modal fade" id="dact<?php echo $li_row['user_id']; ?>">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Deactivate User</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to Deactivate?</p>
                                                            <form method="post" action="processing.php">
                                                                <input type="hidden" name="user_id" value="<?php echo $li_row['user_id']; ?>"><br>
                                                                <input type="hidden" name="url" value="<?php echo $url; ?>"><br>
                                                                <br><button type="submit" class="btn btn-primary" name="submit_dact">Deactivate</button>
                                                            </form><br>
                                                        </div>
                                                        <div class="modal-footer">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Small modal modal end -->
                                            <!-- activate modal start -->
                                            <div class="modal fade" id="act<?php echo $li_row['user_id']; ?>">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Activate User</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to Activate?</p>
                                                            <form method="post" action="processing.php">
                                                                <input type="hidden" name="user_id" value="<?php echo $li_row['user_id']; ?>"><br>
                                                                <input type="hidden" name="url" value="<?php echo $url; ?>"><br>
                                                                <br><button type="submit" class="btn btn-primary" name="submit_act">Activate</button>
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
                                    <h5 class="modal-title">Register User</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">

                                    <!-- login area start -->
                                    <div class="login-area">
                                        <div class="container">
                                                <form action="javascript:;">
                                                    <div class="login-form-head">
                                                        <h4>Register User</h4>
                                                        <p id="formErr"></p>
                                                    </div>
                                                    <div class="login-form-body">
                                                        <div class="form-gp">
                                                            <label for="exampleInputName1">Full Name</label>
                                                            <input type="text" id="fullname" required>
                                                            <i class="ti-user"></i><br>
                                                            <div id="errfn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputEmail1">Email address</label>
                                                            <input type="email" id="email" required>
                                                            <i class="ti-email"></i><br>
                                                            <div id="errem"></div>
                                                        </div>
                                                        <div class="form-gp">                                                            
                                                            <select name="state" id="state" class="custom-select border-0 pr-3" required>
                                                                <option value="" selected="">Select State</option>
                                                                <?php
                                                                $fc = mysqli_query($conn, "SELECT * from state");
                                                                while ($fc_row = mysqli_fetch_array($fc)) {
                                                                    echo '<option value="'.$fc_row['id'].'">'.$fc_row['state_name'].'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputEmail1">Phone Number</label>
                                                            <input type="text" id="phone" required>
                                                            <i class="ti-mobile"></i><br>
                                                            <div id="errpn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputEmail1">Role</label><br>
                                                            <select class="custome-select border-0 pr-3" name="role" id="user_role">
                                                                <option></option>
                                                                <option value="Developer">Developer</option>
                                                                <option value="Support Officer">Support Officer</option>
                                                                <option value="Others">Others</option>
                                                            </select>
                                                            <i class="ti-user"></i><br>
                                                            <div id="errpn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputPassword1">Password</label>
                                                            <input type="password" id="password" required>
                                                            <i class="ti-lock"></i>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputPassword2">Confirm Password</label>
                                                            <input type="password" id="password2" required>
                                                            <i class="ti-lock"></i><br>
                                                            <div id="perr"></div>
                                                        </div>
                                                        <div class="submit-btn-area">
                                                            <input class="btn btn-primary" id="form_submit" type="submit" value="Submit Information">
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

                    var name = $('#fullname').val();
                    var email = $('#email').val();
                    var phone = $('#phone').val();
                    var password = $('#password').val();
                    var password2 = $('#password2').val();
                    var role = $('#user_role').val();
                    var state = $('#state').val();

                    if (name == '' || email == '' || phone == '' || password == '' || password2 == '' || state == '') {
                        $('#formErr').html('<span class="alert alert-danger">Please Fill In All Fields</span>');
                        return false;
                    }
                    else if (name != '' || email != '' || phone != '' || password != '' || password2 != '' || state != '') {
                        $('#formErr').html('');

                        var datastring = 'email='+email;

                        if (password != password2) {
                            $('#form_submit').attr('disabled', 'true');
                            $('#perr').html('<div class="alert alert-danger"><p>Passwords Do Not Match</p></div>');
                            $('#form_submit').removeAttr('disabled');
                            return false;
                        } else {
                            $('#perr').html('');
                        }

                        $.ajax({
                            url: 'ajax/email.php',
                            method: 'post',
                            data: datastring,
                            success: function(msg) {
                                if (msg == 1) {
                                    $('#errem').html('<div class="alert alert-danger"><p>Another User Exists With That Email</p></div>');

                                    return false;

                                } else {
                                    $('#errem').html('');
                                    registerFinal();
                                }
                            }
                        });


                        var datastring = 'name='+name+'&email='+email+'&phone='+phone+'&password='+password+'&role='+role+'&state='+state;

                        function registerFinal() {

                        $.ajax({
                            url: 'ajax/register.php',
                            method: 'post',
                            data: datastring,
                            success: function(msg) {
                                if (msg == 1) {
                                    window.location.replace('user.php');
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
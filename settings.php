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

$email = $_SESSION['email'];

if (isset($_POST['form_submit'])) {

    $name = $_POST['name'];
    $email2 = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $state = $_POST['state'];

    $user = mysqli_query($conn, "UPDATE user set state_id = '$state', user_name = '$name', email = '$email2', phone = '$phone', user_role = '$role' where email = '$email'");

    if ($user) {
        $_SESSION['msg'] = '<span class="alert alert-success">Profile Edited Successfully</span>';
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email2;
        $_SESSION['state_id'] = $state;
    }
}

$user = mysqli_query($conn, "SELECT * from user where email = '$email'");

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>My Profile</title>
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
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-2 mt-5">
                    </div>
                    <div class="col-8 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Manage Your Profile</h4>

                                    <div class="login-area">
                                        <div class="container">
                                                <form action="" method="post">
                                                    <div class="login-form-body">
                                                        <?php 
                                                        if (isset($_SESSION['msg'])) {
                                                            echo $_SESSION['msg'];
                                                            unset($_SESSION['msg']);
                                                        }
                                                        ?>
                                                        <?php
                                                        while ($user_row = mysqli_fetch_array($user)) {
                                                            $id = $user_row['user_id'];
                                                            $name = $user_row['user_name'];
                                                            $email3 = $user_row['email'];
                                                            $phone = $user_row['phone'];
                                                            $role = $user_row['user_role'];
                                                            $state_id = $user_row['state_id'];

                                                            $l = mysqli_query($conn, "SELECT * from state where id='$state_id'");
                                                            while ($l_row = mysqli_fetch_array($l)) { $state_name = $l_row['state_name']; }
                                                        }
                                                        ?>
                                                        <div class="form-gp">
                                                            <input type="text" name="name" id="fullname" value="<?php echo $name; ?>" required>
                                                            <i class="ti-user"></i><br>
                                                            <div id="errfn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
                                                            <i class="ti-email"></i><br>
                                                            <div id="errem"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <input type="text" name="phone" id="phone" value="<?php echo $phone; ?>" required>
                                                            <i class="ti-mobile"></i><br>
                                                            <div id="errpn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputEmail1">State</label><br>
                                                            <select class="custom-select border-0 pr-3" name="state" id="state" required>
                                                                <option value=""> Select One</option>
                                                                <?php
                                                                    $sq = mysqli_query($conn, "SELECT * from state");
                                                                    while ($qs = mysqli_fetch_array($sq)) {
                                                                        echo '<option value="'.$qs['id'].'">'.$qs['state_name'].'</option>';
                                                                    }
                                                                ?>
                                                                
                                                            </select>
                                                            <div id="errpn"></div>
                                                        </div>
                                                        <div class="form-gp">
                                                            <label for="exampleInputEmail1">Role</label><br>
                                                            <select class="custom-select border-0 pr-3" name="role" id="user_role" required>
                                                                <option value=""> Select One</option>
                                                                <option <?php if ($role == 'Developer') { echo 'selected'; } ?> value="Developer">Developer</option>
                                                                <option <?php if ($role == 'Support Officer') { echo 'selected'; } ?> value="Support Officer">Support Officer</option>
                                                                <option <?php if ($role == 'Others') { echo 'selected'; } ?> value="Support Officer">Others</option>
                                                            </select>
                                                            <div id="errpn"></div>
                                                        </div>
                                                        <div class="submit-btn-area">
                                                            <input class="btn btn-primary" id="form_submit" name="form_submit" type="submit" value="Update Information">
                                                        </div>
                                                    </div>
                                                </form>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 mt-5">
                    </div>
                    <!-- Primary table end -->

                    <!-- Large modal start -->
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
                    console.log(876);

                    var name = $('#fullname').val();
                    var email = $('#email').val();
                    var phone = $('#phone').val();
                    var role = $('#user_role').val();

                    if (name == '' || email == '' || phone == '') {
                        $('#formErr').html('<span class="alert alert-danger">Please Fill In All Fields</span>');
                        return false;
                    }
                    else if (name != '' || email != '' || phone != '') {
                        $('#formErr').html('');

                        var datastringg = 'email='+email;

                        $.ajax({
                            url: 'ajax/emails.php',
                            method: 'post',
                            data: datastringg,
                            success: function(msg) {
                                if (msg == 1) {
                                    $('#errem').html('<div class="alert alert-danger"><p>Another User Exists With That Email</p></div>');

                                    return false;

                                }
                            }
                        });
                    }
                    });
                });
        
    </script>
    
    <script type="text/javascript">
        var timoutNow = 3600000; 
        var logoutUrl = 'logout.php';
        var timeoutTimer; 
        // Start timers.
        function StartTimers()
        {
            timeoutTimer = setTimeout("IdleTimeout()", timoutNow);
        }
        // Reset timers.
        function ResetTimers() 
        { 
            console.log('reset');
            clearTimeout(timeoutTimer);
            StartTimers();
            $('#idle_warning').hide();
        }
     
        // Logout the user.
        function IdleTimeout() 
        {
            window.location = logoutUrl;
            $('#idle_warning').show(); 
        }
      $(document).ready(function()
        {
            StartTimers();
            $(document).on('mousemove scroll keyup keypress mousedown mouseup mouseover',function(){
            ResetTimers();
            });
        });
     
        $(window).on('load',function()
        {
            
            $(window).on('mousemove scroll keyup keypress mousedown mouseup mouseover',function(){
            ResetTimers();
            });
            
        });
    </script>

</html>
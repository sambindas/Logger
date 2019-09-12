<?php
error_reporting(0);
session_start();
require 'connection.php';
require 'functions.php';

if ($_GET['token'] != '') {
    $token = $_GET['token'];
    $query = mysqli_query($conn, "SELECT * from user where token = '$token'");
    if (mysqli_num_rows($query) < 1) {
        $_SESSION['msg'] = '<span class="alert alert-danger">Invalid Password Token</span>';
        header("Location: login.php");
        exit();
    } else {
        if (isset($_POST['create'])) {
            $password1 = $_POST['password1'];
            $password2 = $_POST['password2'];

            if ($password1 == $password2) {
                $pass = sha1($password2);
                $reset = mysqli_query($conn, "UPDATE user set password = '$pass' where token = '$token'");
                if ($reset) {
                    mysqli_query($conn, "UPDATE user set token = 0 where token = '$token'");
                    $_SESSION['msg'] = '<span class="alert alert-danger">Password Was Reset Successfully</span>';
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['msg'] = '<span class="alert alert-danger">Something Went Wrong, Please Try Again,</span>';
                    header("Location: reset.php?token=".$token."");
                    exit();
                }
            } else {
                $_SESSION['msg'] = '<span class="alert alert-danger">Passwords Do Not Match</span>';
                header("Location: reset.php?token=".$token."");
                exit();
            }
        }
    }
} else {
    $_SESSION['msg'] = '<span class="alert alert-danger">Invalid Password Token</span>';

    header("Location: login.php");
    exit();
}
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Recover Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <!-- modernizr css -->
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- login area start -->
    <div class="login-area">
        <div class="container">
            <div class="login-box ptb--100">
                <form action="" method="post">
                    <div class="login-form-head">
                        <h4>Create Password</h4>
                        <p>Create A New Password</p><br>
                        <?php 
                        if (isset($_SESSION['msg'])) {
                            echo $_SESSION['msg'];
                            unset($_SESSION['msg']);
                        }
                        ?>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Create a New Password</label>
                            <input type="password" required name="password1" id="password1">
                            <i class="ti-lock"></i>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Confirm New Password</label>
                            <input type="password" required name="password2" id="password2">
                            <i class="ti-lock"></i>
                        </div>
                        <div class="submit-btn-area mt-5">
                            <button id="create" name="create" type="submit">Create New Password <i class="ti-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->

    <!-- jquery latest version -->
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>

</html>
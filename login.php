<?php
session_start();

if (isset($_SESSION['logged_user'])) {
    $l_u = $_SESSION['logged_user'];
    if ($l_u == 'client') {
        header("Location: clientindex.php");
    } elseif ($l_u == 'support') {
        header("Location: index.php");
    }  
    }

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login</title>
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
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- login area start -->
    <div class="login-area">
        <div class="container">
            <div class="login-box ptb--100">
                <form action="javascript:;">
                    <div class="login-form-head">
                        <img src="logo.jpeg"><br><br>
                        <p>Sign in to use the Incident log</p><br>
                        <p><?php 
                        if (isset($_SESSION['msg'])) {
                            echo $_SESSION['msg'];
                            unset($_SESSION['msg']);
                        }
                        ?></p>
                        <p id="formErr"></p>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" id="email">
                            <i class="ti-email"></i>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" id="password">
                            <i class="ti-lock"></i>
                        </div>
                        <div class="row mb-4 rmber-area">
                            <div class="col-6 text-right">
                                <a href="reset-pass.php">Forgot Password?</a>
                            </div>
                        </div>
                        <input type="hidden" id="user_type" name="user_type" value="eclat">
                        <div class="submit-btn-area">
                            <input value="Submit" id="form_submit" class="btn btn-primary" type="submit">
                            <div id="loade"></div>
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

    <script type="text/javascript">
        $(document).ready(function(){

            $('#form_submit').click(function(){
                $(this).fadeOut();
                $('#loade').html('<img src="assets/images/eclipse.gif">');
                var email = $('#email').val();
                var password = $('#password').val();
                var user_type = $('#user_type').val();

                if (email == '' || password == '') {
                    $('#formErr').html('<span class="alert alert-danger">Please Fill In All Fields</span>');
                    $(this).fadeIn();
                $('#loade').html('');
                    return false;
                } else {

                    $('#formErr').html('');


                    var datastring = 'email='+email+'&password='+password+'&user_type='+user_type;

                    $.ajax({
                        url: 'ajax/login.php',
                        method: 'post',
                        data: datastring,
                        success: function(msg){
                            if (msg == 'support') {
                                window.location.replace('index.php');
                            } else if (msg == 'client') {
                                window.location.replace('clientindex.php');
                            } else {
                                $('#formErr').html('<span class="alert alert-danger">Authentication Failed!</span>');
                                $('#form_submit').fadeIn();
                                $('#loade').html('');
                                return false;
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
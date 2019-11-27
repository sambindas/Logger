<?php

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$noww = date('M Y');

$facility_id = $_GET['facility_id'];
$fac = mysqli_query($conn, "SELECT * from facility where id = $facility_id");
while ($f = mysqli_fetch_array($fac)) {
    $fa = $f['name'];
}

if (isset($_POST['submit_me'])) {
    $type = $_POST['type'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    $insert = mysqli_query($conn, "INSERT INTO server_access (type, password, username, facility) VALUES ('$type', '$password', '$username', '$fa')");
    if ($insert) {
        $_SESSION['msg'] = '<span class="alert alert-success">Access Added Successfully.</span>';
    }
}


    if (isset($_POST['edit'])) {
        $facility_id = $_POST['facility_id'];
        $type = $_POST['type'];
        $password = $_POST['password'];
        $username = $_POST['username'];

        mysqli_query($conn, "UPDATE server_access set type = '$type', username='$username', password='$password' where id = '$facility_id'");
        $_SESSION['msg'] = '<span class="alert alert-success">Record Edited Successfully.</span>';
    }
    if (isset($_POST['delete'])) {
        $facility_id = $_POST['facility_id'];

        mysqli_query($conn, "DELETE from server_access where id = '$facility_id'");
        $_SESSION['msg'] = '<span class="alert alert-success">Record Deleted Successfully.</span>';
    }

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Add Server Access</title>
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
            ?><br>
                    <div class="container">
                        Server Access for <b><?php echo $fa; ?></b>
                                <?php 
                                if (isset($_SESSION['msg'])) {
                                    echo $_SESSION['msg'];
                                    unset($_SESSION['msg']);
                                }
                                ?><br><br>
                        <div class="col-md-12">
                            <div class="input_fields_wrap">
                                <div class="row">
                                    <div class="col-md-6">
                                        <form action="" method="post">
                                            <div class="form-group" >
                                                <div class="form-group">
                                                <label for="">Access Type</label>
                                                <select name="type" class="form-control" required="">
                                                    <option value="">--Select Access Type--</option>
                                                    <option value="Teamviewer">Teamviewer</option>
                                                    <option value="AnyDesk">AnyDesk</option>
                                                    <option value="Putty">Putty</option>
                                                    <option value="Server">Server </option>
                                                </select>
                                                <label for="">Username or ID (If Applicable)</label>
                                                <input name="username" type="text" class="form-control" required="">
                                            </div>
                                            <div class="form-group">
                                                <label for="">Password (If Applicable)</label>
                                                <input name="password" type="text" required class="form-control">
                                            </div>
                                            </div>
                                        <button type="submit" name="submit_me" style="background-color:green;" class="btn btn-info active">Submit</button>
                                    </form>
                                </div>
                            </div>
                        <div><hr><hr>
                            <?php 
                            $i = mysqli_query($conn, "SELECT * from server_access where facility = '$fa'");
                                
                            while ($ir = mysqli_fetch_array($i)) {
                                echo "
                                <b>".$ir['type']."</b><br> Username: ".$ir['username']." | Password: ".$ir['password']."
                                <button class='btn btn-sm btn-danger' data-toggle='modal' data-target='#del".$ir['id']."'>Delete</button>
                                <button class='btn btn-sm btn-primary' data-toggle='modal' data-target='#edit".$ir['id']."'>Edit</button>
                                <Br><hr><br>";
                            ?>
                            <!-- edit modal start -->
                            <div class="modal fade" id="edit<?php echo $ir['id']; ?>">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit This Record</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="post">
                                                    <div class="form-group" >
                                                        <div class="form-group">
                                                        <label for="">Access Type</label>
                                                        <select name="type" class="form-control" required="">
                                                            <option <?php if($ir['type'] == "Teamviewer") echo "SELECTED";?> value="Teamviewer">Teamviewer</option>
                                                            <option <?php if($ir['type'] == "AnyDesk") echo "SELECTED";?> value="AnyDesk">AnyDesk</option>
                                                            <option <?php if($ir['type'] == "Putty") echo "SELECTED";?> value="Putty">Putty</option>
                                                            <option <?php if($ir['type'] == "Server") echo "SELECTED";?> value="Server">Server </option>
                                                        </select>
                                                        <label for="">Username or ID (If Applicable)</label>
                                                        <input name="username" type="text" class="form-control" value="<?php echo $ir['username']; ?>" required="">
                                                    </div>
                                                    <input type="hidden" name="facility_id" value="<?php echo $ir['id']; ?>">
                                                    <div class="form-group">
                                                        <label for="">Password (If Applicable)</label>
                                                        <input name="password" value="<?php echo $ir['password']; ?>" type="text" required class="form-control">
                                                    </div>
                                                    </div>
                                                <button type="submit" name="edit" style="background-color:green;" class="btn btn-info active">Edit</button>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- delete modal -->
                            <div class="modal fade" id="del<?php echo $ir['id']; ?>">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete This Record</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="">
                                                <input type="hidden" name="facility_id" value="<?php echo $ir['id']; ?>">
                                                <br><button type="submit" class="btn btn-danger" name="delete">Delete</button>
                                            </form><br>
                                        </div>
                                        <div class="modal-footer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Small modal modal end -->
                        <?php } ?>
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
    <!-- <script>
        $(document).ready(function() {
        var max_fields = 15; //maximum input boxes allowed
        var wrapper = $(".input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
        x++; //text box increment
        $(wrapper).append('<div class="row"><div class="col-md-6"><div class="form-group"><label for="">Email</label><input name="email[]" type="text" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label for="">Numbers</label><input name="number[]" type="text" class="form-control"></div></div><div style="cursor:pointer;background-color:red;" class="remove_field btn btn-info">Remove</div></div>'); //add input box
        }
        });
        $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
        })
        });
    </script> -->
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

</html>
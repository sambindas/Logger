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

$issue_id = $_GET['issue_id'];

$editqq = mysqli_query($conn, "SELECT * from issue where issue_id = '$issue_id'");
$editq = mysqli_query($conn, "SELECT * from issue where issue_id = '$issue_id'");

while ($t = mysqli_fetch_array($editqq)) {
    if ($t['support_officer'] != $_SESSION['id'] || $t['status'] == 3) {

     $_SESSION['msg'] = '<span class="alert alert-danger">Cannot Edit Another Users Issue.</span>';
    header("Location: index.php ");
}
}

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Edit an Incident</title>
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

                    <?php
                    while ($li_row = mysqli_fetch_array($editq)) {
                        $selected = $li_row['priority'];
                        $selected1 = $li_row['issue_type'];
                        $selected2 = $li_row['issue_level'];
                        $facc = $li_row['facility'];

                    ?>
                    <div class="container">
                        <div class="">
                            <form method="post" action="processing.php" name="issue_form" id="issue_form" enctype="multipart/form-data">
                                <div class="login-form-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Facility</h4>
                                                <select name="facility" class="custome-select border-0 pr-3" required>
                                                    <option selected="">Select One</option>
                                                    <?php
                                                    $fc = mysqli_query($conn, "SELECT * from facility");
                                                    while ($fc_row = mysqli_fetch_array($fc)) {
                                                    
                                                    ?>
                                                    <option <?php if ($fc_row['code'] == $facc) {
                                                        echo "selected";
                                                    } ?> value="<?php echo $fc_row['code'];?>"><?php echo $fc_row['name'] ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Type</h4>
                                                <select name = "type" class="custome-select border-0 pr-3" required>
                                                    <option disabled selected="">Select One</option>
                                                    <option <?php if($selected1 == "Issue") echo "SELECTED";?> value="Issue">Issue</option>
                                                    <option <?php if($selected1 == "Request") echo "SELECTED";?> value="Request">Request</option>
                                                    <option <?php if($selected1 == "Other") echo "SELECTED";?> value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-gp">
                                                <h4  class="header-title mb-0">Incident Level</h4>
                                                <select name="il" id="il" class="custome-select border-0 pr-3" required>
                                                    <option disabled selected="">Select One</option>
                                                    <option <?php if($selected2 == "1") echo "SELECTED";?> value="1">Level One (1 hr - 24 hrs)</option>
                                                    <option <?php if($selected2 == "2") echo "SELECTED";?> value="2">Level Two (24 hrs - 1 wk)</option>
                                                    <option <?php if($selected2 == "3") echo "SELECTED";?> value="3">Level Three (1 wk - 1mth)</option>
                                                    <option <?php if($selected2 == "4") echo "SELECTED";?> value="4">Level Four (TBD)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="header-title mb-0">Incident</h4>
                                    <textarea required cols="73" rows="6" type="text" id="issue" name="issue" placeholder="issue"><?php echo $li_row['issue'] ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'issue' );
                                    </script><br>
                                    <div class="row"> 
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Incident Client Reporter</h4>
                                                <input type="text" name="icr" id="icr" value="<?php echo $li_row['issue_client_reporter'] ?>" required>
                                            </div>
                                        </div>
                                        <input type="hidden" name="issue_id" value="<?php echo $issue_id; ?>">
                                        <input type="hidden" name="url" value="<?php echo $url; ?>">
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Affected Department(s)</h4>
                                                <input type="text" name="ad" id="ad" value="<?php echo $li_row['affected_dept'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Priority</h4>
                                                <select name="priority" class="custome-select border-0 pr-3" required>
                                                    <option disabled selected="">Select One</option>
                                                    <option <?php if($selected == "High") echo "SELECTED";?> value="High">High</option>
                                                    <option <?php if($selected == "Medium") echo "SELECTED";?> value="Medium">Medium</option>
                                                    <option <?php if($selected == "Low") echo "SELECTED";?> value="Low">Low</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Issue Reported On</h4>
                                                <input type="text" id="datetimepicker" value="<?php echo $li_row['issue_reported_on'] ?>" name="iro">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="Submit" name="edit_issue" value="Submit Incident" style="float: right;" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
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

    <script type="text/javascript">
        $(document).ready(function(){
            jQuery('#datetimepicker').datetimepicker();
            $('#filters').click(function(){
                    $('#filterid').toggle();
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
             timepicker:false,
             format:'d/m/Y'
            });
        });
    </script>

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
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

if (isset($_GET['id'])) {
    $aid = $_GET['id'];
}

$e_a = mysqli_query($conn, "SELECT * from activity where id = $aid");

while ($ea = mysqli_fetch_array($e_a)) {
        $activity = $ea['activity'];
        $facility_name = $ea['facility'];
        $visit_type = $ea['visit_type'];
        $status = $ea['status'];
        $pstatus = $ea['pstatus'];
        $activity_date = $ea['activity_date'];
        $comments = $ea['comments'];
        $user_id = $ea['user_id'];
    }

if ($user_id != $_SESSION['id']) {
    $_SESSION['msg'] = "<span class='alert alert-danger'>Cannot Edit Another User's Activity.</span>";
    header('Location: activity.php');
    exit();
}

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Edit Activity</title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6/css/select2.min.css" rel="stylesheet"/>
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
                    <div class="container">
                        <div class="">
                            <form method="post" action="activityprocessing.php" name="issue_form" id="issue_form" enctype="multipart/form-data">
                                <div class="login-form-body">
                                    <div class="row">
                                        <?php 
                                            if (isset($_SESSION['msg'])) {
                                                echo $_SESSION['msg'];
                                                unset($_SESSION['msg']);
                                            }
                                        ?>
                                        <div class="col-sm-3">
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Facility Name</h4>
                                                <select name="facility" class="custom-select border-0 pr-3" required>
                                                    <option selected="">Select One</option>
                                                    <?php
                                                    $fc = mysqli_query($conn, "SELECT * from facility");
                                                    while ($fc_row = mysqli_fetch_array($fc)) {
                                                    
                                                    ?>
                                                    <option <?php if ($fc_row['code'] == $facility_name) {
                                                        echo "selected";
                                                    } ?> value="<?php echo $fc_row['code'];?>"><?php echo $fc_row['name'] ?></option>
                                                <?php } ?>
                                                    <option <?php if ($facility_name == 'Others') {echo 'selected';} ?> value="Others">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Visit Type</h4>
                                                <select name="visit_type" class="custom-select border-0 pr-3" required>
                                                    <option value="">Select One</option>
                                                    <option  <?php if ($visit_type == 'Onsite') {echo 'selected';} ?> value="Onsite">Onsite</option>
                                                    <option <?php if ($visit_type == 'Remote') {echo 'selected';} ?> value="Remote">Remote</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Status</h4>
                                                <select name="status" class="custom-select border-0 pr-3" required>
                                                    <option value="" selected="">Select One</option>
                                                    <option <?php if ($status == 'Complete (All Done)') {echo 'selected';} ?> value="Complete (All Done)">Complete (All Done)</option>
                                                    <option <?php if ($status == 'Incomplete (Partially Done)') {echo 'selected';} ?> value="Incomplete (Partially Done)">Incomplete (Partially Done)</option>
                                                    <option <?php if ($status == 'Pending (Escalated)') {echo 'selected';} ?> value="Pending (Escalated)">Pending (Escalated)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="a_id" value="<?php echo $aid; ?>">
                                        <input type="hidden" name="url" value="<?php echo $url; ?>">
                                        <div class="col-sm-4">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Activity Date</h4>
                                                <input type="text" id="datetimepicker" name="activity_date" value="<?php echo $activity_date; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="header-title mb-0">Activity Description</h4>
                                    <textarea cols="73" rows="6" type="text" id="issue" name="activity" placeholder="issue" required><?php echo $activity; ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'issue' );
                                    </script><br>
                                    <h4 class="header-title mb-0">Status Comments</h4>
                                    <textarea cols="73" rows="6" type="text" id="comments" name="comments" placeholder="comments" required><?php echo $comments; ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'comments' );
                                    </script><br>
                                    <h4 class="header-title mb-0">Previous Status</h4>
                                    <textarea cols="73" rows="6" type="text" id="pstatus" name="pstatus" placeholder="previous status" required><?php echo $pstatus; ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'pstatus' );
                                    </script><br>
                                </div>
                                <input type="Submit" name="edit_activity" value="Edit Activity" style="float: right;" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <footer>
            <div class="footer-area">
                <p>Â© Copyright 2019. All right reserved.</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var curr = new Date;
            var firstday = new Date(curr.setDate(curr.getDate() - curr.getDay()));
            jQuery('#datetimepicker').datetimepicker({
                timepicker: false,
                maxDate: '0d',
                minDate: firstday
                });
                
            });

         $('#submit_issue').click(function(){
            var issue = $('#issue').val();

            if (issue == '') {
                alert('Please Fill the issue');
                return false;
            }
         });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#rotimi').select2();
        });
    </script>

</html>
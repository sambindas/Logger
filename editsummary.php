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

$user_id = $_SESSION['id'];

if (isset($_GET['month']) and isset($_GET['year']) and isset($_GET['week'])) {
    $month = $_GET['month'];
    $week = $_GET['week'];
    $year = $_GET['year'];
    $wik = $_GET['week_token'];

}

$q = mysqli_query($conn, "SELECT * from activity where month = '$month' and year = '$year' and week = '$week' and user_id = '$user_id'");
while ($rq = mysqli_fetch_array($q)) {
    $unplanned = $rq['unplanned'];
    $planned = $rq['planned'];
    $unresolved = $rq['unresolved'];
    $issues = $rq['issues'];
}

function getStartAndEndDate($week, $year) {
  $dto = new DateTime();
  $dto->setISODate($year, $week);
  $ret1 = $dto->format('d-m-Y');
  $dto->modify('+6 days');
  $ret2 = $dto->format('d-m-Y');
  $final = $ret1.' to '.$ret2;
  return $final;
}

$week_array = getStartAndEndDate($wik, $year);

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Edit Week Summary</title>
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
                                        <h5>Edit Summary For Week <?php  echo $week_array; ?></h5><br><br>
                                        <?php 
                                            if (isset($_SESSION['msg'])) {
                                                echo $_SESSION['msg'];
                                                unset($_SESSION['msg']);
                                            }
                                        ?>
                                        <input type="hidden" name="url" value="<?php echo $url; ?>">
                                        <input type="hidden" name="week" value="<?php echo $week; ?>">
                                        <input type="hidden" name="year" value="<?php echo $year; ?>">
                                        <input type="hidden" name="month" value="<?php echo $month; ?>">
                                    </div><br><br>
                                    <h4 class="header-title mb-0">Unplanned Activities</h4>
                                    <textarea cols="73" rows="6" type="text" id="unplanned" name="unplanned" placeholder="unplanned" required><?php echo $unplanned; ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'unplanned' );
                                    </script><br>
                                    <h4 class="header-title mb-0">Unresolved Incidents</h4>
                                    <textarea cols="73" rows="6" type="text" id="unresolved" name="unresolved" placeholder="unresolved" required><?php echo $unresolved; ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'unresolved' );
                                    </script><br>
                                    <h4 class="header-title mb-0">Planned Activities (Coming Week)</h4>
                                    <textarea cols="73" rows="6" type="text" id="planned" name="planned" placeholder="Planned" required><?php echo $planned; ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'planned' );
                                    </script><br>
                                    <h4 class="header-title mb-0">Issues for Management Attention</h4>
                                    <textarea cols="73" rows="6" type="text" id="issues" name="issues" placeholder="Issues" required><?php echo $issues; ?></textarea>
                                    <script>
                                        CKEDITOR.replace( 'issues' );
                                    </script><br>
                                </div>
                                <input type="Submit" name="submit_summary" value="Submit Summary" style="float: right;" class="btn btn-primary">&nbsp;&nbsp;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var curr = new Date;
            var firstday = new Date(curr.setDate(curr.getDate() - curr.getDay()+1));
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
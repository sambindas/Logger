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
unset($_SESSION['url']);
$_SESSION['url'] = $url;

$noww = date('M Y');
if (isset($_GET['month']) && isset($_GET['week']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $week = $_GET['week'];
    $year = $_GET['year'];
}
$query = "SELECT * from activity where month = '$month' and week = '$week' and year = '$year'";

$dow = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View Week Activity For All Users</title>
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
    <style type="text/css">
        
    </style>
</head>

<body id="mybody">
            <?php
            require 'sidebar.php';
            require 'header.php';
            ?>

            <div class="container"><br>
                <a href="newactivity.php" id="newissue" class="btn btn-primary btn-flat">New Activity</a><br><br>
                <?php 
                    if (isset($_SESSION['msg'])) {
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                    }
                ?>
                <h1>Activity For Week <?php echo "$week of $month $year"; ?></h1><br><br>

                <!-- <div class="form-group" style="margin-bottom:20px">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <span class="label" style="background:#b2bfd4 !important;"><a href="#tuesday">Tuesday</a></span>
                            <span class="label" style="background:#b2bfd4 !important;"><a href="#wednesday">Wednesday</a></span>
                            <span class="label" style="background:#b2bfd4 !important;"><a href="#thursday">Thursday</a></span>
                            <span class="label" style="background:#b2bfd4 !important;"><a href="#friday">Friday</a></span>
                            <span class="label" style="background:#b2bfd4 !important;"><a href="#saturday">Saturday</a></span>
                            <span class="label" style="background:#b2bfd4 !important;"><a href="#sunday">Sunday</a></span>
                        </div>
                    </div>
                </div> -->

                
                <?php 
                foreach ($dow as $days) {
                    echo '<h3>'.$days.'</h3><br>
                    <table class="table text-center" border="1" style="padding: 10px; width: 100%">';
                    $query2 = "SELECT * from activity where month = '$month' and week = '$week' and day = '$days' and year = '$year'";
                    $q = mysqli_query($conn, $query2);
                    echo '
                    <thead>
                        <tr>                                            
                            <th> Officer </th>
                            <th> Facilty Name </th>
                            <th> Activity Description </th>
                            <th> Status / Visit Type </th>
                            <th> Status Comments </th>
                            <th> Previous Status </th>
                            <th> Date Logged </th>
                        </tr>
                    </thead>';
                        while ($run = mysqli_fetch_array($q)) {
                            $ud = $run['user_id'];
                            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * from user where user_id = '$ud'"));
                            echo '  
                                <tr> 
                                    <td align="center">'.(($ud == $_SESSION['id']) ?
                                         '<a href="activityprocessing.php?did='.$run['id'].'&ud='.$ud.'">
                                    <i class="ti-trash data-toggle="dropdown">
                                    </i>
                                    </a>
                                    <a href="editactivity.php?id='.$run['id'].'">
                                    <i class="ti-pencil data-toggle="dropdown">
                                    </i>
                                    </a><br>' : '' ).'
                                    '.$user['user_name'].'</td>
                                    <td align="center">'.$run['facility'].'</td>
                                    <td align="left">'.$run['activity'].'</td>
                                    <td align="center">'.$run['status'].' <br> <hr> '.$run['visit_type'].'</td>
                                    <td align="left">'.$run['comments'].'</td>
                                    <td align="left">'.$run['pstatus'].'</td>
                                    <td align="center">'.$run['date_submitted'].'</td>
                                </tr>';
                        }
                        echo '</table><br>';

                    }
                ?>
                <h2>Other Summaries</h2>
                <table class="table text-center" border="1" style="padding: 10px; width: 100%">
                <?php 
                $query2 = "SELECT * from activity where month = '$month' and week = '$week' and year = '$year' group by user_id";
                $q = mysqli_query($conn, $query2);
                echo '
                <thead>
                    <tr>                                            
                        <th> Officer </th>
                        <th> Unplanned Activities </th>
                        <th> Unresolved Incidents </th>
                        <th> Planned Activities (Coming Week) </th>
                        <th> Issues For Management Attention </th>
                    </tr>
                </thead>';
                    while ($run = mysqli_fetch_array($q)) {
                        $ud = $run['user_id'];
                        $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * from user where user_id = '$ud'"));
                        echo '  
                            <tr> 
                                <td align="left">'.$user['user_name'].'</td>
                                <td align="left">'.$run['unplanned'].'</td>
                                <td align="left">'.$run['unresolved'].'</td>
                                <td align="left">'.$run['planned'].'</td>
                                <td align="left">'.$run['issues'].'</td>
                            </tr>';
                    }
                    
                    ?>

                </table><br>
                
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
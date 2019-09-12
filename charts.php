<?php
session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();
if ($_SESSION['logged_user'] == 'client') {
    header('Location: clientindex.php');
}
//if someone filters
if (isset($_GET['from']) and isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
}

// request time average

$sum_issue = 0;
$sum_res = 0;

$time_averag = "SELECT issue_date, resolution_date from issue where issue_type = 'request' and (status = 3 or status = '2')";

if ($from != '' and $to != '') {
    $time_averag .= "and fissue_date between '$from' and '$to'";
}
$time_average = mysqli_query($conn, $time_averag);

while ($time_avg = mysqli_fetch_array($time_average)) {
  $issue_d = $time_avg['issue_date'];
  $res  = $time_avg['resolution_date'];

  $sum_f = strtotime($issue_d);
  $sum_r = strtotime($res);

  $sum_issue += $sum_f;
  $sum_res += $sum_r;
}

$final = $sum_res  - $sum_issue;
$fnumber =  mysqli_num_rows($time_average);
$fd = $final / $fnumber;
//this function calculates the average time
$avg_resolve_time = secondsToTime($fd);

//issue time average

$sum_issuei = 0;
$sum_resi = 0;

$time_averagi = "SELECT issue_date, resolution_date from issue where issue_type = 'issue' and (status = 3 or status = '2')";

if ($from != '' and $to != '') {
    $time_averagi .= "and fissue_date between '$from' and '$to'";
}
$time_averagei = mysqli_query($conn, $time_averagi);

while ($time_avgi = mysqli_fetch_array($time_averagei)) {
  $issue_di = $time_avgi['issue_date'];
  $resi  = $time_avgi['resolution_date'];

  $sum_fi = strtotime($issue_di);
  $sum_ri = strtotime($resi);

  $sum_issuei += $sum_fi;
  $sum_resi += $sum_ri;
}

$finali = $sum_resi  - $sum_issuei;
$fnumberi =  mysqli_num_rows($time_averagei);
$fdi = $finali / $fnumberi;
//this function calculates the average time
$avg_resolve_timei = secondsToTime($fdi);

$resul = "SELECT count(issue_id) as numberr, facility from issue";
if ($from != '' and $to != '') {
    $resul .= " where fissue_date between '$from' and '$to' group by facility";
} else {
  $resul .= " group by facility";
}
$result = mysqli_query($conn, $resul);

$lin = "SELECT count(issue_id) as numberrrr, month, issue_id from issue";
if ($from != '' and $to != '') {
    $lin .= " where fissue_date between '$from' and '$to' group by month order by issue_id asc";
} else {
  $lin .= " group by month order by issue_id asc";
}

$line = mysqli_query($conn, $lin);

$userqq = "SELECT count(issue.issue_id) as numberrr, issue.support_officer, user.user_name from issue inner join user on issue.support_officer = user.user_id";
if ($from != '' and $to != '') {
    $userqq .= " where fissue_date between '$from' and '$to' group by support_officer";
} else {
  $userqq .= " group by support_officer";
}

$dev = "SELECT user from issue where user != ''";
if ($from != '' and $to != '') {
    $dev .= " and fissue_date between '$from' and '$to' group by user";
} else {
  $dev .= " group by user";
}
$devq = mysqli_query($conn, $dev);
$userq = mysqli_query($conn, $userqq);

$resulttt = "SELECT count(issue.issue_id) as numb, issue.facility, facility.name from issue inner join facility on issue.facility = facility.code";
if ($from != '' and $to != '') {
    $resulttt .= " where fissue_date between '$from' and '$to' group by facility order by numb asc";
} else {
  $resulttt .= " group by facility order by numb asc";
}
$resultt = mysqli_query($conn, $resulttt);

while ($numb = mysqli_fetch_array($resultt)) {
  $f = $numb['name'];
  $n = $numb['numb'];
}
$developer_id = [];
while ($develop = mysqli_fetch_array($devq)) {
    $developer_id[] = $develop['user'];
}
$mon = [];
$numberrrr = [];

while ($roo = mysqli_fetch_array($line)) {
  $mon[] = $roo['month'];
  $numberrrr[] = $roo['numberrrr'];
}

$usern = [];
$user = [];
$uname = [];

while ($ro = mysqli_fetch_array($userq)) {
  $usern[] = $ro['numberrr'];
  $user[] = $ro['support_officer'];
  $uname[] = $ro['user_name'];
}

$number = [];
$facility = [];

while ($row = mysqli_fetch_array($result)) {
    $number[] = $row['numberr'];
    $facility[] = $row['facility'];
}

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <style>
        .c0, .c4, .c5, .c9, .d0, .d4, .d5, .d9 {
            color: pink;
            font-weight: bolder;
            font-family: 'Georgia', sans-serif;
        }
    </style>
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
                                <li><span>Analytics</span></li>
                                <li><span></span></li>
                                <li><span></span></li>
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

            <div class="container">
              <form action="" method="get" id="filterid">

              <div class="form-group row">
                  <div class="col-sm-2">
                      <label for="ex1">Show Data From</label>
                      <?php
                      if ($from != '') {
                          echo '<input type="text" name="from" class="form-control" id="datetimepicker1" value="'.$from.'" readonly placeholder="From" name="from"><i class="ti-calender"></i>';
                      } else {
                          echo '<input type="text" name="from" class="form-control" id="datetimepicker1" value="'.date('Y-m-01').'" readonly placeholder="From" name="from"><i class="ti-calender"></i>';
                      }
                      ?>
                  </div>
                  <div class="col-sm-2">
                      <label for="ex1">To</label>
                      <?php
                      if ($to != '') {
                          echo '<input type="text" class="form-control" id="datetimepicker2" value="'.$to.'" readonly placeholder="To" name="to"><i class="ti-calender"></i>';
                      } else {
                          echo '<input type="text" class="form-control" id="datetimepicker2" value="'.date('Y-m-d').'" readonly placeholder="To" name="to"><i class="ti-calender"></i>';
                      }
                      ?>
                  </div> &nbsp;
                  <div class="col-sm-2">
                  <label>&nbsp;&nbsp;</label><br>
                      <input type="submit" id="filter" class="btn-flat btn btn-primary btn-xs" value="Filter">
                      <a type="button" href="charts.php" class="btn-flat btn btn-primary btn-xs">Clear Filter</a>
                  </div>
              </div>
              <hr>
            </form><br>
            <?php
              if ($to != '') {
                  echo 'SHOWING DATA FOR: <b>'.$from.' <b/>to <b>'.$to.'</b>';
              } else {
                  echo 'SHOWING ALL DATA';
              }
              ?>
            </div>
            <div class="main-content-inner">
                <!-- bar chart start -->
                <div class="row">
                    <div class="col-lg-6 mt-5">
                        <div class="card">
                            <div class="card-body" style="float: left;">
                                <canvas id="bar-chart" width="600px" height="400px"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-5">
                        <div class="card">
                            <div class="card-body" style="float: right;">
                                <canvas id="bar-chart2" width="600px" height="400px"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" style="float: left;">
                                <canvas id="bar-chart3" width="1000px" height="400px"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" style="float: right;">
                              <!-- Hoverable Rows Table start -->
                              <div class="col-lg-12 mt-5">
                                <h4 class="header-title">Incident Reporter Stats</h4>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table table-dark text-center">
                                            <thead class="text-uppercase bg-info">
                                                <tr>
                                                    <th scope="col">Incident Reporter</th>
                                                    <th scope="col">Total Incidents</th>
                                                    <th scope="col">Open</th>
                                                    <th scope="col">Not An Issue</th>
                                                    <th scope="col">Marked Unclear</th>
                                                    <th scope="col">Incompletely Done</th>
                                                    <th scope="col">Incomplete Info</th>
                                                    <th scope="col">Done (Treated)</th>
                                                    <th scope="col">Closed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($user as $uid) {
                                                $sql = "SELECT count(*) as incident_count, user.user_name, issue.support_officer from issue inner join user on issue.support_officer = user.user_id where support_officer = '$uid'"; 
                                                if ($from != '' and $to != '') {
                                                    $sql .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status0 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and (status = 0 or status = 8)";
                                                if ($from != '' and $to != '') {
                                                    $status0 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status1 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 1";
                                                if ($from != '' and $to != '') {
                                                    $status1 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status2 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 2";
                                                if ($from != '' and $to != '') {
                                                    $status2 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status3 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 3";
                                                if ($from != '' and $to != '') {
                                                    $status3 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status4 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 4";
                                                if ($from != '' and $to != '') {
                                                    $status4 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status5 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 5";
                                                if ($from != '' and $to != '') {
                                                    $status5 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status6 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 6";
                                                if ($from != '' and $to != '') {
                                                    $status6 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status7 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 7";
                                                if ($from != '' and $to != '') {
                                                    $status7 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status8 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 8";
                                                if ($from != '' and $to != '') {
                                                    $status8 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status9 = "SELECT count(*) as status_count, issue_id FROM issue where support_officer = '$uid' and status = 9";
                                                if ($from != '' and $to != '') {
                                                    $status9 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $statu0 = mysqli_fetch_array(mysqli_query($conn, $status0));
                                                $statu1 = mysqli_fetch_array(mysqli_query($conn, $status1));
                                                $statu2 = mysqli_fetch_array(mysqli_query($conn, $status2));
                                                $statu3 = mysqli_fetch_array(mysqli_query($conn, $status3));
                                                $statu4 = mysqli_fetch_array(mysqli_query($conn, $status4));
                                                $statu5 = mysqli_fetch_array(mysqli_query($conn, $status5));
                                                $statu6 = mysqli_fetch_array(mysqli_query($conn, $status6));
                                                $statu7 = mysqli_fetch_array(mysqli_query($conn, $status7));
                                                $statu8 = mysqli_fetch_array(mysqli_query($conn, $status8));
                                                $statu9 = mysqli_fetch_array(mysqli_query($conn, $status9));
                                                
                                                $open = implode(',', $statu0['issue_id']);
                                                $dbut = implode(',', $statu1['issue_id']);

                                                $run = mysqli_query($conn, $sql);
                                                while ($runn = mysqli_fetch_array($run)) {
                                                    if ($from != '' and $to != '') {
                                                        echo '  <tr>
                                                                <th scope="row">'.$runn['user_name'].'</th>
                                                                <td>'.$runn['incident_count'].'</td>
                                                                <td><a class="c0" id="'.$uid.'$0$'.$from.'$'.$to.'" href="javascript:;">'.$statu0['status_count'].'</a></td>
                                                                <td>'.$statu2['status_count'].'</td>
                                                                <td><a class="c5" id="'.$uid.'$5$'.$from.'$'.$to.'" href="javascript:;">'.$statu5['status_count'].'</a></td>
                                                                <td><a class="c4" id="'.$uid.'$4$'.$from.'$'.$to.'" href="javascript:;">'.$statu4['status_count'].'</a></td>
                                                                <td><a class="c9" id="'.$uid.'$9$'.$from.'$'.$to.'" href="javascript:;">'.$statu9['status_count'].'</a></td>
                                                                <td>'.$statu1['status_count'].'</td>
                                                                <td>'.$statu3['status_count'].'</td>
                                                            </tr>';
                                                    } else {
                                                    echo '  <tr>
                                                                <th scope="row">'.$runn['user_name'].'</th>
                                                                <td>'.$runn['incident_count'].'</td>
                                                                <td><a class="c0" id="'.$uid.'$0$" href="javascript:;">'.$statu0['status_count'].'</a></td>
                                                                <td>'.$statu2['status_count'].'</td>
                                                                <td><a class="c5" id="'.$uid.'$5$" href="javascript:;">'.$statu5['status_count'].'</a></td>
                                                                <td><a class="c4" id="'.$uid.'$4$" href="javascript:;">'.$statu4['status_count'].'</a></td>
                                                                <td><a class="c9" id="'.$uid.'$9$" href="javascript:;">'.$statu9['status_count'].'</a></td>
                                                                <td>'.$statu1['status_count'].'</td>
                                                                <td>'.$statu3['status_count'].'</td>
                                                            </tr>';
                                                        }
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                              </div>
                              <!-- Hoverable Rows Table end -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" style="float: right;">
                              <!-- Hoverable Rows Table start -->
                              <div class="col-lg-12 mt-5">
                                <h4 class="header-title">Incident Resolution Stats</h4>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table table-dark text-center">
                                            <thead class="text-uppercase bg-warning">
                                                <tr>
                                                    <th scope="col">Assignee</th>
                                                    <th scope="col">Total Incidents</th>
                                                    <th scope="col">Open</th>
                                                    <th scope="col">Not An Issue</th>
                                                    <th scope="col">Marked Unclear</th>
                                                    <th scope="col">Incompletely Done</th>
                                                    <th scope="col">Incomplete Info</th>
                                                    <th scope="col">Done (Treated)</th>
                                                    <th scope="col">Closed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($developer_id as $dev_id) {
                                                
                                                $sql_dev = "SELECT count(*) as incident_count from issue where user = '$dev_id'"; 
                                                if ($from != '' and $to != '') {
                                                    $sql_dev .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev0 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and (status 8)";
                                                if ($from != '' and $to != '') {
                                                    $status_dev0 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev1 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 1";
                                                if ($from != '' and $to != '') {
                                                    $status_dev1 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev2 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 2";
                                                if ($from != '' and $to != '') {
                                                    $status_dev2 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev3 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 3";
                                                if ($from != '' and $to != '') {
                                                    $status_dev3 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev4 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 4";
                                                if ($from != '' and $to != '') {
                                                    $status_dev4 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev5 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 5";
                                                if ($from != '' and $to != '') {
                                                    $status_dev5 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev6 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 6";
                                                if ($from != '' and $to != '') {
                                                    $status_dev6 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev7 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 7";
                                                if ($from != '' and $to != '') {
                                                    $status_dev7 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev8 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 8";
                                                if ($from != '' and $to != '') {
                                                    $status_dev8 .= " and fissue_date between '$from' and '$to'";
                                                }
                                                $status_dev9 = "SELECT count(*) as status_count, issue_id FROM issue where user = '$dev_id' and status = 9";
                                                if ($from != '' and $to != '') {
                                                    $status_dev9 .= " and fissue_date between '$from' and '$to'";
                                                }

                                                $statu_dev0 = mysqli_fetch_array(mysqli_query($conn, $status_dev0));
                                                $statu_dev1 = mysqli_fetch_array(mysqli_query($conn, $status_dev1));
                                                $statu_dev2 = mysqli_fetch_array(mysqli_query($conn, $status_dev2));
                                                $statu_dev3 = mysqli_fetch_array(mysqli_query($conn, $status_dev3));
                                                $statu_dev4 = mysqli_fetch_array(mysqli_query($conn, $status_dev4));
                                                $statu_dev5 = mysqli_fetch_array(mysqli_query($conn, $status_dev5));
                                                $statu_dev6 = mysqli_fetch_array(mysqli_query($conn, $status_dev6));
                                                $statu_dev7 = mysqli_fetch_array(mysqli_query($conn, $status_dev7));
                                                $statu_dev8 = mysqli_fetch_array(mysqli_query($conn, $status_dev8));
                                                $statu_dev9 = mysqli_fetch_array(mysqli_query($conn, $status_dev9));

                                                $run = mysqli_query($conn, $sql_dev);
                                                while ($runn = mysqli_fetch_array($run)) {
                                                    if ($from != '' and $to != '') {
                                                    echo '  <tr>
                                                                <th scope="row">'.$dev_id.'</th>
                                                                <td>'.$runn['incident_count'].'</td>
                                                                <td><a class="d0" id="'.$dev_id.'$8$'.$from.'$'.$to.'" href="javascript:;">'.$statu_dev8['status_count'].'</a></td>
                                                                <td>'.$statu_dev2['status_count'].'</td>
                                                                <td><a class="d5" id="'.$dev_id.'$5$'.$from.'$'.$to.'" href="javascript:;">'.$statu_dev5['status_count'].'</a></td>
                                                                <td><a class="d4" id="'.$dev_id.'$4$'.$from.'$'.$to.'" href="javascript:;">'.$statu_dev4['status_count'].'</a></td>
                                                                <td><a class="d9" id="'.$dev_id.'$9$'.$from.'$'.$to.'" href="javascript:;">'.$statu_dev9['status_count'].'</a></td>
                                                                <td>'.$statu_dev1['status_count'].'</td>
                                                                <td>'.$statu_dev3['status_count'].'</td>
                                                            </tr>';
                                                    } else {
                                                    echo '  <tr>
                                                                <th scope="row">'.$dev_id.'</th>
                                                                <td>'.$runn['incident_count'].'</td>
                                                                <td><a class="d0" id="'.$dev_id.'$8$ href="javascript:;">'.$statu_dev8['status_count'].'</a></td>
                                                                <td>'.$statu_dev2['status_count'].'</td>
                                                                <td><a class="d5" id="'.$dev_id.'$5$ href="javascript:;">'.$statu_dev5['status_count'].'</a></td>
                                                                <td><a class="d4" id="'.$dev_id.'$4$ href="javascript:;">'.$statu_dev4['status_count'].'</a></td>
                                                                <td><a class="d9" id="'.$dev_id.'$9$ href="javascript:;">'.$statu_dev9['status_count'].'</a></td>
                                                                <td>'.$statu_dev1['status_count'].'</td>
                                                                <td>'.$statu_dev3['status_count'].'</td>
                                                            </tr>';
                                                    }
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                              </div>
                              <!-- Hoverable Rows Table end -->
                            </div>
                        </div>
                    </div>                    
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body" style="float: right;">
                              <!-- Hoverable Rows Table start -->
                              <div class="col-lg-6 mt-5">
                                <h4 class="header-title">Quick Data</h4>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table table-hover text-center">
                                            <thead class="text-uppercase">
                                                <tr>
                                                    <th scope="col">Data</th>
                                                    <th scope="col">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Average Resolution Time (Request)</th>
                                                    <td><?php echo $avg_resolve_time; ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Average Resolution Time (Issue)</th>
                                                    <td><?php echo $avg_resolve_timei; ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Facility with Most Incidents</th>
                                                    <td><?php echo ''.$f.' ('.$n.')'; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                        <div class="modal fade" id="yes" role="dialog">
                                            <div class="modal-dialog">
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Issue IDs</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                    
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                              </div>
                              <!-- Hoverable Rows Table end -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- bar chart end -->
            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <footer>
            <div class="footer-area">
            </div>
        </footer>
        <!-- footer area end-->
    </div>
    <!-- page container area end -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
    <!-- jquery latest version -->
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    
    <!-- bootstrap 4 js -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <script>
    new Chart(document.getElementById("bar-chart"), {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($facility); ?>,
      datasets: [
        {
          label: "Total Incidents Submitted per Facility",
          backgroundColor: "#3e95cd",
          data:<?php echo json_encode($number); ?>
        }
      ]
    },
    options: {        scales: {
        yAxes: [{
            display: true,
            ticks: {
                suggestedMin: 0,    // minimum will be 0, unless there is a lower value.
                // OR //
                beginAtZero: true   // minimum value will be 0.
            }
        }]
    },

      responsive: false,
      maintainAspectRatio: false,
      legend: { display: true },
      title: {
        display: true,
        text: 'Incident Log Data For All Facilities'
      }
    }
});
  </script>
  <script>
    new Chart(document.getElementById("bar-chart2"), {
    type: 'pie',
    data: {
      labels: <?php echo json_encode($uname); ?>,
      datasets: [
        {
          label: "Total Incidents Submitted per User",
          backgroundColor: ["#3e95cd", "#7D998B", "#0bfd84", "#fdb60b", "#dd988c", "#dc8cdd", "#af8cdd", "#535469", "#d28e9c", "#d22411", "#adb6a9"],
          data:<?php echo json_encode($usern); ?>
        }
      ]
    },
    options: {
      responsive: false,
      maintainAspectRatio: false,
      legend: { display: true },
      title: {
        display: true,
        text: 'Incident Log Submitted per User'
      }
    }
});
  </script>
   <script>
    new Chart(document.getElementById("bar-chart3"), {
    type: 'line',
    data: {
      labels: <?php echo json_encode($mon); ?>,
      datasets: [
        {
          label: "Total Incidents Submitted per Month",
          backgroundColor: "#7DC8f8",
          data:<?php echo json_encode($numberrrr); ?>
        }
      ]
    },
    options: {
      responsive: false,
      maintainAspectRatio: false,
      legend: { display: true },
      title: {
        display: true,
        text: 'Incident Log Data For All Facilities Per Month'
      }
    }
});
  </script>
  <script src="jquery.datetimepicker.full.min.js"></script>
    <script src="jquery.datetimepicker.js"></script>
    <script type="text/javascript">
       $(document).ready(function(){
            jQuery('#datetimepicker2').datetimepicker({
                format: 'Y-m-d',
                timepicker:false,
                maxDate: '0d',
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
             format:'Y-m-d',
             timepicker:false,
             maxDate: '0d',
            });
        });
    </script>
    <script>
        $(document).ready(function(){

            $('.c0').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 't0';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });

            $('.c5').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 't5';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });

            $('.c9').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 't9';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });

            $('.c4').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 't4';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });

            //for developers
            $('.d0').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 'td0';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });

            $('.d5').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 'td5';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });

            $('.d9').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 'td9';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });

            $('.d4').click(function(){
                let id = this.id;
                let splitid = id.split('$');
                let userid = splitid[0];
                let from = splitid[2];
                let to = splitid[3];
                let status = splitid[1];
                let type = 'td4';

                // AJAX request
                $.ajax({
                url: 'ajax/charts_id.php',
                type: 'post',
                data: {type: type, userid: userid, from: from, to: to, status: status},
                success: function(response){ 
                    // Add response in Modal body
                    $('.modal-body').html(response);

                    // Display Modal
                    $('#yes').modal('show'); 
                }
                });
            });
        });
    </script>
</body>

</html>

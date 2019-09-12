<?php
error_reporting(0);
session_start();
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = "index.php";
include('../connection.php');
include('../functions.php');
include('c.php');

$state_id = $_SESSION['state_id'];
$state_array = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37);
$s_array = join(",", $state_array);
$column = array('issue_id', 'facility', 'issue_type', 'incident', 'priority', 'support_officer', 'issue_date');

$noww = date('M Y');
if ($_SESSION['id'] == 1 or $_SESSION['id'] == 6) {
    $query = "SELECT * FROM issue where state_id IN($s_array)";
} else {
    $query = "SELECT * FROM issue where state_id = '$state_id'";
}


if ($_POST['datetimepicker1'] != '' || $_POST['datetimepicker2'] != '') {
	$query .= ' and fissue_date between "'.$_POST['datetimepicker1'].'" and "'.$_POST['datetimepicker2'].'"';
} else {
	$query .= ' and month = "'.$noww.'"';
}

if(isset($_POST['filter_status']) && $_POST['filter_status'] != '')
{
 $query .= ' and 
 status = "'.$_POST['filter_status'].'" 
 ';
}

if(isset($_POST['view']))
{
 $query .= ' and 
 (type = '.$_POST['view'].' or type = 2) 
 ';
}

if(isset($_POST['filter_assign']) && $_POST['filter_assign'] != '')
{
 $query .= ' and 
 user = "'.$_POST['filter_assign'].'" 
 ';
}

if(isset($_POST['logger']) && $_POST['logger'] != '')
{
 $query .= ' and 
 support_officer = "'.$_POST['logger'].'" 
 ';
}

if(isset($_POST['search_table']) && $_POST['search_table'] != '')
{
 $query .= ' and 
 (issue like "%'.$_POST['search_table'].'%" or issue_type like "%'.$_POST['search_table'].'%" or facility like "%'.$_POST['search_table'].'%" or issue_id like "%'.$_POST['search_table'].'%" or priority like "%'.$_POST['search_table'].'%")
 ';
}

if(isset($_POST['order']))
{
 $query .= 'ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'ORDER BY issue_id DESC ';
}

// print_r($query);
// die();

$query1 = '';

if($_POST["length"] != -1)
{
 $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $con->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $con->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();



$data = array();

foreach($result as $row)
{
$status = $row['status'];
$user = $row['user'];
$issue_id = $row['issue_id'];
$name_type = $row['type'];

$log = '';

$logq = mysqli_query($conn, "SELECT * from movement where issue_id = '$issue_id'");
if (mysqli_num_rows($logq) >= 1) {
while ($lq = mysqli_fetch_array($logq)) {
    $done_by = $lq['done_by'];
    $done_at = $lq['done_at'];
    $movement = $lq['movement'];
    $n = 1;

    $mq = mysqli_query($conn, "SELECT * from user where user_id = '$done_by'");
    while ($rmq = mysqli_fetch_array($mq)) {
        $mu = $rmq['user_name'];
    $log .= '<b>'.$movement.' </b> - '.$mu.' <i> @ '.$done_at.'</i><br>'; 
    }
} } else {
$log = "<p>No Movements For This Incident</p>";
}


$s1 = $row['support_officer'];
$s2 = $row['resolved_by'];

$date_one = $row['issue_date'];
$date_two = $row['resolution_date']; 

$date_onets = strtotime($date_one);
$date_twots = strtotime($date_two);

$final_date = $date_twots - $date_onets;
$so2 = '';
$q1 = mysqli_query($conn, "SELECT * from user where user_id = '$s1'");
while ($rq1 = mysqli_fetch_array($q1)) {
    $so1 = $rq1['user_name'];

    $q2 = mysqli_query($conn, "SELECT * from user where user_id = '$s2'");
    while ($rq2 = mysqli_fetch_array($q2)) {
    $so2 = $rq2['user_name'];

} }

$reas = '';
$ccc = '';
$cfm = '';

$fc = mysqli_query($conn, "SELECT * from user where status = 1 and user_name != '$user'");
while ($c_row = mysqli_fetch_array($fc)) {
    $cr = $c_row['user_name'];
    $reas .= '<option value="'.$cr.'">'.$c_row['user_name'].'</option>';
}

$rrb = $row['resolved_by'];
$rb = mysqli_query($conn, "SELECT * from user where user_id = '$rrb'");
while ($rbr = mysqli_fetch_array($rb)) {
    $cfm = $rbr['user_name'] .'<br>'. $row['resolution_date'] ; }

$reqa = "<div class='modal fade' id='req".$row['issue_id']."'>
            <div class='modal-dialog modal-notify modal-primary'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='heading lead'>Requires Approval</h5>
                        <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                    </div>
                    <div class='modal-body'>
                        <p>Add Additional Comments If Available</p>
                        <form method='post' action='processing.php'>
                            <textarea type='text' cols='40' name='ncomments'></textarea>
                            <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                            <input type='hidden' name='url' value='".$url."'><br>
                            <br><button type='submit' class='btn btn-primary' name='submit_req'>Mark</button>
                        </form><br>
                    </div>
                    <div class='modal-footer'>
                    </div>
                </div>
            </div>
        </div>";
  $commentsq = mysqli_query($conn, "SELECT * from comments where issue_id = '$issue_id'");
    if (mysqli_num_rows($commentsq) >= 1) {
    while ($cq = mysqli_fetch_array($commentsq)) {
        $uid = $cq['user'];
        $ui = mysqli_query($conn, "SELECT * from user where user_id = '$uid'");
        while ($rui = mysqli_fetch_array($ui)) {
            $userrr = $rui['user_name'];
        	$sstatus = $cq['status']; 

                if ($sstatus == 0) {
                    $ccc .='<b>'.$userrr.' - (Reopened):</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                } elseif ($sstatus == 1) {
                    $ccc .= '<b>'.$userrr.' - (Done):</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                } elseif ($sstatus == 2) {
                    $ccc .= '<b>'.$userrr.' - (Not An Issue):</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                } elseif ($sstatus == 4) {
                    $ccc .= '<b>'.$userrr.' - (Incomplete):</b> '.$cq['comment']. ' <i> @ '.$cq['date_added'].'</i><br>'; 
                } elseif ($sstatus == 5) {
                    $ccc .= '<b>'.$userrr.' - (Not Clear):</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                } elseif ($sstatus == 6) {
                    $ccc .= '<b>'.$userrr.' - (Require Approval):</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                } elseif ($sstatus == 7) {
                    $ccc .= '<b>'.$userrr.' - (Disapproved):</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                } elseif ($sstatus == 8) {
                    $ccc .='<b>'.$userrr.' - (Approved):</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                } else {
                    $ccc .= '<b>'.$userrr.' - :</b> '.$cq['comment'].' <i> @ '.$cq['date_added'].'</i><br>'; 
                }
                
            }
        } }else {
            $ccc .='<p>No Comments For This Incident</p>';
        }
$summary = "<div class='modal fade bd-example-modal-lg' id='sum".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary modal-lg'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Summary of this Incident</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body' style='text-align: left;'>
                            
                            <p><b>Facility:</b> ".$row['facility']."</p>
                            <p><b>Type:</b> ".$row['issue_type']."</p>
                            <p><b>Level:</b> ".$row['issue_level']."</p>
                            <p><b>Priotity:</b> ".$row['priority']."</p>
                            <p><b>Incident:</b> ".$row['issue']."</p>
                            <p><b>Incident reported on:</b> ".$row['issue_reported_on']." by ". $row['issue_client_reporter']."</p>
                            <p><b>Submitted by:</b> ".$so1." on ". $row['issue_date']."</p>
                            <p><b>Resolved by:</b> ".$so2 ." on ". $row['resolution_date']."</p>
                            <p><b>Info Relayed to:</b> ".$row['info_relayed_to']." by ".$row['info_medium']."</p>
                            <p><b>Incident was resolved in:</b> ".secondsToTime($final_date)."</p>
                            
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$send_to_client = "<div class='modal fade' id='send".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary modal-notify modal-primary modal-sm'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Copy Incident to User Dashboard</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <form method='post' action='processing.php'>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='send_to_client'>Copy Incident</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$acc = "<div class='modal fade' id='comm".$row['issue_id']."'>
            <div class='modal-dialog modal-notify modal-primary' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='heading lead'>Add Comments</h5>
                        <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                    </div>
                    <div class='modal-body'>
                        <p>Add Comments to this Incident</p>
                        <form method='post' action='processing.php'>
                            <textarea type='text' cols='40' name='comments' required></textarea>
                            <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                            <input type='hidden' name='url' value='".$url."'><br>
                            <div class='modal-footer justify-content-right'>
                            <br><button type='submit' class='btn btn-primary' name='submit_comm'>Submit</button>
                        </form><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
$reassign = "<div class='modal fade' id='re".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary modal-notify modal-primary modal-sm'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Reassign Incident</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Currently assigned to</p>
                            ".$user."<br><br>
                            <form method='post' action='processing.php'>
                                <select name='reassign' required>
                                    <option value=''>Select New User</option>
                                    ".$reas."
                                </select><br><Br>
                                <input type='checkbox' checked='checked' name='smail'>
                                Send Mail Notification<Br>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='submit_re'>Reassign</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$comm = "<div class='modal fade bd-example-modal-sm' id='comments".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary modal-notify modal-primary modal-lg'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>View Incident Comments</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body' style='text-align: left;'>".$ccc."
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$nai = "<div class='modal fade' id='nai".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Add Additional Comments</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Add Additional Comments If Available</p>
                            <form method='post' action='processing.php'>
                                <textarea type='text' cols='40' name='ncomments' required></textarea>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='submit_nai'>Mark as Not an Issue</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$noc = "<div class='modal fade' id='noc".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Mark as Not Clear</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Add Additional Comments If Available</p>
                            <form method='post' action='processing.php'>
                                <textarea type='text' cols='40' name='dcomments'></textarea>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='submit_noc'>Mark as Not Clear</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$media = "<div class='modal fade and carousel slide' id='".$issue_id."media'>
            <div class='modal-dialog modal-notify modal-primary'>
              <div class='modal-content'>
                <div class='modal-body'>

                   <div id='dynamic_slide_show' class='carousel slide' data-ride='carousel'>
                    <ol class='carousel-indicators'>
                    ".make_slide_indicators($conn, $issue_id)."
                    </ol>

                    <div class='carousel-inner'>
                     ".make_slides($conn, $issue_id)."
                    </div>

                   </div>

                  <script type='text/javascript'>
                        $(document).ready(function() {
                            $('a.gallery').featherlightGallery({
                              
                            }); 
                        });
                    </script>
                </div>
              </div>
            </div>
        </div>";
$reopen = "<div class='modal fade' id='reo".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Add Additional Comments</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Add Additional Comments If Available</p>
                            <form method='post' action='processing.php'>
                                <textarea type='text' cols='40' name='rcomments'></textarea>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='submit_reo'>Mark as Reopened</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$approved = "<div class='modal fade' id='app".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Approval</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Add Additional Comments</p>
                            <form method='post' action='processing.php'>
                                <textarea cols='40' name='comments'></textarea>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='submit_app'>Approved</button>
                                <button type='submit' class='btn btn-danger' name='submit_dapp'>Not Approved</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$incomplete = "<div class='modal fade' id='icm".$row['issue_id']."'>
                    <div class='modal-dialog modal-notify modal-primary'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='heading lead'>Mark as Incomplete</h5>
                                <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                            </div>
                            <div class='modal-body'>
                                <p>Add Additional Comments If Available</p>
                                <form method='post' action='processing.php'>
                                    <textarea type='text' cols='40' name='dcomments' required></textarea>
                                    <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                    <input type='hidden' name='url' value='".$url."'><br>
                                    <br><button type='submit' class='btn btn-primary' name='submit_icm'>Mark as Incomplete</button>
                                </form><br>
                            </div>
                            <div class='modal-footer'>
                            </div>
                        </div>
                    </div>
                </div>";
$confirmed = "<div id='con".$row['issue_id']."' class='modal fade bd-example-modal-lg'>
                <div class='modal-dialog modal-notify modal-primary modal-lg'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Confirm Incident ".$issue_id." Has Been Solved</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <div class='container'>
                                <form method='post' action='processing.php' id='issue_form' enctype='multipart/form-data'>
                                    <div class='login-form-body'>
                                        <div class='row'> 
                                            <div class='col-sm-4'>           
                                                <div class='form-gp'>
                                                    <h4 class='header-title mb-0'>Resolved By</h4>
                                                    <p>
                                                    ".$cfm."
                                                     </p>
                                                </div>
                                            </div>
                                            <div class='col-sm-4'>           
                                                <div class='form-gp'>
                                                    <h4 class='header-title mb-0'>Info Relayed To</h4>
                                                    <input type='text' name='irt' id='irt'>
                                                </div>
                                            </div>
                                            <input type='hidden' name='issue_id' value='".$issue_id."'>
                                            <input type='hidden' name='url' value='".$url."'>
                                            <div class='col-sm-4'>           
                                                <div class='form-gp'>
                                                    <h4 class='header-title mb-0'>Info Medium</h4>
                                                    <input type='text' name='im' id='im'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type='Submit' name='confirmed' value='Confirmed' style='float: right;' class='btn btn-primary'>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
$done = "<div class='modal fade' id='done".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Add Additional Comments</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Add Additional Comments If Available</p>
                            <form method='post' action='processing.php'>
                                <textarea type='text' cols='40' name='dcomments'></textarea>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='submit_done'>Mark as Done</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$iip = "<div class='modal fade' id='iip".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Mark As Incomplete Information Provided</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <p>Add Additional Comments If Available</p>
                            <form method='post' action='processing.php'>
                                <textarea type='text' cols='40' name='dcomments' required></textarea>
                                <input type='hidden' name='issue_id' value='".$row['issue_id']."'><br>
                                <input type='hidden' name='url' value='".$url."'><br>
                                <br><button type='submit' class='btn btn-primary' name='submit_iip'>Mark as Incomplete Information</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
$movement = "<div class='modal fade bd-example-modal-sm' id='logs".$row['issue_id']."'>
                <div class='modal-dialog modal-notify modal-primary modal-lg'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>View Incident Movement</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body' style='text-align: left;'>
                            ".$log."
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";
if ($status == 0 or $status == 8 and $row['type']==0 or $row['type']==2) {
    $actions = "  <div class='dropdown'>
                <button class='btn btn-xs btn-primary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                Action
                </button>
                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                    <a data-toggle='modal' data-target='#done".$row['issue_id']."' class='dropdown-item' href='#'>Done</a>
                    <a data-toggle='modal' data-target='#nai".$row['issue_id']."' class='dropdown-item' href='#'>Not an Issue</a>
                    <a data-toggle='modal' data-target='#noc".$row['issue_id']."' class='dropdown-item' href='#'>Not Clear</a>
                    <a data-toggle='modal' data-target='#req".$row['issue_id']."' class='dropdown-item' href='#'>Requires Approval</a>
                    <a data-toggle='modal' data-target='#iip".$row['issue_id']."' class='dropdown-item' href='#'>Incomplete Information</a>
                <div class='dropdown-divider'></div>
                    <a data-toggle='modal' data-target='#comm".$row['issue_id']."' class='dropdown-item' href='#'>Add Comments</a>
                    <a data-toggle='modal' data-target='#comments".$row['issue_id']."' class='dropdown-item' href='#'>View Comments</a>
                <div class='dropdown-divider'></div>
                    <a class='dropdown-item' href='image.php?issue_id=".$row['issue_id']."'>Upload Media</a>
                    <a class='dropdown-item' data-toggle='modal' href='#".$row['issue_id']."media'>View Media</a>
                <div class='dropdown-divider'></div>
                    <a class='dropdown-item' href='edit.php?issue_id=".$row['issue_id']."'>Edit Incident</a>
                    <a class='dropdown-item' data-toggle='modal' href='#re".$row['issue_id']."'>Reassign Incident</a>
                    <a class='dropdown-item' data-toggle='modal' href='#logs".$row['issue_id']."'>View Incident Movement</a>
                </div>
            </div>
            ".$reqa."
            ".$noc."
            ".$nai."
            ".$comm."
            ".$done."
            ".$media."
            ".$reassign."
            ".$iip."
            ".$acc."
            ".$movement."
            ".$send_to_client."
            ";
    }
    elseif ($status == 0 or $status == 8 and $row['type'] == 1) {
        $actions = "  <div class='dropdown'>
                <button class='btn btn-xs btn-primary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                Action
                </button>
                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                    <a data-toggle='modal' data-target='#send".$row['issue_id']."' class='dropdown-item' href='#'>Copy Incident</a>
                    <a data-toggle='modal' data-target='#done".$row['issue_id']."' class='dropdown-item' href='#'>Done</a>
                    <a data-toggle='modal' data-target='#nai".$row['issue_id']."' class='dropdown-item' href='#'>Not an Issue</a>
                    <a data-toggle='modal' data-target='#noc".$row['issue_id']."' class='dropdown-item' href='#'>Not Clear</a>
                    <a data-toggle='modal' data-target='#req".$row['issue_id']."' class='dropdown-item' href='#'>Requires Approval</a>
                    <a data-toggle='modal' data-target='#iip".$row['issue_id']."' class='dropdown-item' href='#'>Incomplete Information</a>
                <div class='dropdown-divider'></div>
                    <a data-toggle='modal' data-target='#comm".$row['issue_id']."' class='dropdown-item' href='#'>Add Comments</a>
                    <a data-toggle='modal' data-target='#comments".$row['issue_id']."' class='dropdown-item' href='#'>View Comments</a>
                <div class='dropdown-divider'></div>
                    <a class='dropdown-item' href='image.php?issue_id=".$row['issue_id']."'>Upload Media</a>
                    <a class='dropdown-item' data-toggle='modal' href='#".$row['issue_id']."media'>View Media</a>
                <div class='dropdown-divider'></div>
                    <a class='dropdown-item' href='edit.php?issue_id=".$row['issue_id']."'>Edit Incident</a>
                    <a class='dropdown-item' data-toggle='modal' href='#re".$row['issue_id']."'>Reassign Incident</a>
                    <a class='dropdown-item' data-toggle='modal' href='#logs".$row['issue_id']."'>View Incident Movement</a>
                </div>
            </div>
            ".$reqa."
            ".$noc."
            ".$nai."
            ".$comm."
            ".$done."
            ".$media."
            ".$reassign."
            ".$iip."
            ".$acc."
            ".$movement."
            ".$send_to_client."
            ";
    
} elseif ($status == 1) {
    $actions = "  <div class='dropdown'>
	                <button class='btn btn-xs btn-primary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
	                Action
	                </button>
	                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
	                    <a data-toggle='modal' data-target='#con".$row['issue_id']."' class='dropdown-item' href='#'>Confirmed</a>
	                    <a data-toggle='modal' data-target='#icm".$row['issue_id']."' class='dropdown-item' href='#'>Incomplete</a>
	                    <a data-toggle='modal' data-target='#reo".$row['issue_id']."' class='dropdown-item' href='#'>Reopen</a>
	                <div class='dropdown-divider'></div>
	                    <a data-toggle='modal' data-target='#comm".$row['issue_id']."' class='dropdown-item' href='#'>Add Comments</a>
	                    <a data-toggle='modal' data-target='#comments".$row['issue_id']."' class='dropdown-item' href='#'>View Comments</a>
	                <div class='dropdown-divider'></div>
	                    <a class='dropdown-item' data-toggle='modal' href='#".$row['issue_id']."media'>View Media</a>
	                <div class='dropdown-divider'></div>
	                    <a class='dropdown-item' href='edit.php?issue_id=".$row['issue_id']."'>Edit Incident</a>
	                    <a class='dropdown-item' data-toggle='modal' href='#logs".$row['issue_id']."'>View Issue Movement</a></div>
	                </div>
           		 </div>
            ".$media."
            ".$confirmed."
            ".$comm."
            ".$incomplete."
            ".$reopen."
            ".$confirmed."
            ".$acc."
            ".$movement."
           		 "
           		 ;
            
} elseif ($status == 2) {
    $actions = '  <div class="dropdown">
                <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a data-toggle="modal" data-target="#reo'.$row['issue_id'].'" class="dropdown-item" href="#">Reopen</a>
                <div class="dropdown-divider"></div>
                    <a data-toggle="modal" data-target="#comm'.$row['issue_id'].'" class="dropdown-item" href="#">Add Comments</a>
                    <a data-toggle="modal" data-target="#comments'.$row['issue_id'].'" class="dropdown-item" href="#">View Comments</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" data-toggle="modal" href="#'.$row['issue_id'].'media">View Media</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="edit.php?issue_id='.$row['issue_id'].'">Edit Incident</a>
                    <a class="dropdown-item" data-toggle="modal" href="#logs'.$row['issue_id'].'">View Incident Movement</a></div>
                </div>
            </div>

            '.$reopen.'
            '.$comm.'
            '.$media.'
            '.$movement.'
            '.$acc.'
            ';
            
} elseif ($status == 3) {
    $actions = '  <div class="dropdown">
                <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a data-toggle="modal" data-target="#sum'.$row['issue_id'].'" class="dropdown-item" href="#">View Summary</a>
                <div class="dropdown-divider"></div>
                    <a data-toggle="modal" data-target="#comm'.$row['issue_id'].'" class="dropdown-item" href="#">Add Comments</a>
                    <a data-toggle="modal" data-target="#comments'.$row['issue_id'].'" class="dropdown-item" href="#">View Comments</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" data-toggle="modal" href="#'.$row['issue_id'].'media">View Media</a>
                    <a class="dropdown-item" data-toggle="modal" href="#logs'.$row['issue_id'].'">View Incident Movement</a></div>
                </div>
            </div>
            '.$summary.'
            '.$comm.'
            '.$media.'
            '.$movement.'
            '.$acc.'
'
            ;                                                            
} elseif ($status == 4) {
    $actions = '  <div class="dropdown">
                <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a data-toggle="modal" data-target="#done'.$row['issue_id'].'" class="dropdown-item" href="#">Done</a>
                    <a data-toggle="modal" data-target="#reo'.$row['issue_id'].'" class="dropdown-item" href="#">Reopen</a>
                <div class="dropdown-divider"></div>
                    <a data-toggle="modal" data-target="#comm'.$row['issue_id'].'" class="dropdown-item" href="#">Add Comments</a>
                    <a data-toggle="modal" data-target="#comments'.$row['issue_id'].'" class="dropdown-item" href="#">View Comments</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="image.php?issue_id='.$row['issue_id'].'">Upload Media</a>
                    <a class="dropdown-item" data-toggle="modal" href="#'.$row['issue_id'].'media">View Media</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="edit.php?issue_id='.$row['issue_id'].'">Edit Incident</a>
                    <a class="dropdown-item" data-toggle="modal" href="#re'.$row['issue_id'].'">Reassign Incident</a>
                    <a class="dropdown-item" data-toggle="modal" href="#logs'.$row['issue_id'].'">View Incident Movement</a></div>
                </div>
            </div>

            '.$done.'
            '.$reopen.'
            '.$comm.'
            '.$media.'
            '.$reassign.'
            '.$movement.'
            '.$acc.'
            ';
        }
 elseif ($status == 5) {
    $actions = '  <div class="dropdown">
                <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a data-toggle="modal" data-target="#done'.$row['issue_id'].'" class="dropdown-item" href="#">Done</a>
                    <a data-toggle="modal" data-target="#reo'.$row['issue_id'].'" class="dropdown-item" href="#">Reopen</a>
                <div class="dropdown-divider"></div>
                    <a data-toggle="modal" data-target="#comm'.$row['issue_id'].'" class="dropdown-item" href="#">Add Comments</a>
                    <a data-toggle="modal" data-target="#comments'.$row['issue_id'].'" class="dropdown-item" href="#">View Comments</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="image.php?issue_id='.$row['issue_id'].'">Upload Media</a>
                    <a class="dropdown-item" data-toggle="modal" href="#'.$row['issue_id'].'media">View Media</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="edit.php?issue_id='.$row['issue_id'].'">Edit Incident</a>
                    <a class="dropdown-item" data-toggle="modal" href="#re'.$row['issue_id'].'">Reassign Incident</a>
                    <a class="dropdown-item" data-toggle="modal" href="#logs'.$row['issue_id'].'">View Incident Movement</a></div>
            </div>

            '.$done.'
            '.$reopen.'
            '.$comm.'
            '.$media.'
            '.$reassign.'
            '.$movement.'
            '.$acc.'
            ';
        }
 elseif ($status == 6) {
    $actions = '  <div class="dropdown">
                <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a data-toggle="modal" data-target="#app'.$row['issue_id'].'" class="dropdown-item" href="#">Approval Status</a>
                <div class="dropdown-divider"></div>
                    <a data-toggle="modal" data-target="#comm'.$row['issue_id'].'" class="dropdown-item" href="#">Add Comments</a>
                    <a data-toggle="modal" data-target="#comments'.$row['issue_id'].'" class="dropdown-item" href="#">View Comments</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="image.php?issue_id='.$row['issue_id'].'">Upload Media</a>
                    <a class="dropdown-item" data-toggle="modal" href="#'.$row['issue_id'].'media">View Media</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" data-toggle="modal" href="#logs'.$row['issue_id'].'">View Incident Movement</a></div>
                </div>
            </div>

            '.$comm.'
            '.$media.'
            '.$movement.'
            '.$acc.'
            '.$approved.'
            ';
        }
 elseif ($status == 7) {
    $actions = '  <div class="dropdown">
                <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <div class="dropdown-divider"></div>
                    <a data-toggle="modal" data-target="#comm'.$row['issue_id'].'" class="dropdown-item" href="#">Add Comments</a>
                    <a data-toggle="modal" data-target="#comments'.$row['issue_id'].'" class="dropdown-item" href="#">View Comments</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="image.php?issue_id='.$row['issue_id'].'">Upload Media</a>
                    <a class="dropdown-item" data-toggle="modal" href="#'.$row['issue_id'].'media">View Media</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" data-toggle="modal" href="#logs'.$row['issue_id'].'">View Incident Movement</a></div>
                </div>
            </div>

            '.$comm.'
            '.$movement.'
            '.$media.'
            '.$acc.'
            ';
} elseif ($status == 9) {
            $actions = "  <div class='dropdown'>
                <button class='btn btn-xs btn-primary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                Action
                </button>
                <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                    <a data-toggle='modal' data-target='#done".$row['issue_id']."' class='dropdown-item' href='#'>Done</a>
                    <a data-toggle='modal' data-target='#nai".$row['issue_id']."' class='dropdown-item' href='#'>Not an Issue</a>
                    <a data-toggle='modal' data-target='#noc".$row['issue_id']."' class='dropdown-item' href='#'>Not Clear</a>
                    <a data-toggle='modal' data-target='#req".$row['issue_id']."' class='dropdown-item' href='#'>Requires Approval</a>
                <div class='dropdown-divider'></div>
                    <a data-toggle='modal' data-target='#comm".$row['issue_id']."' class='dropdown-item' href='#'>Add Comments</a>
                    <a data-toggle='modal' data-target='#comments".$row['issue_id']."' class='dropdown-item' href='#'>View Comments</a>
                <div class='dropdown-divider'></div>
                    <a class='dropdown-item' href='image.php?issue_id=".$row['issue_id']."'>Upload Media</a>
                    <a class='dropdown-item' data-toggle='modal' href='#".$row['issue_id']."media'>View Media</a>
                <div class='dropdown-divider'></div>
                    <a class='dropdown-item' href='edit.php?issue_id=".$row['issue_id']."'>Edit Incident</a>
                    <a class='dropdown-item' data-toggle='modal' href='#re".$row['issue_id']."'>Reassign Incident</a>
                    <a class='dropdown-item' data-toggle='modal' href='#logs".$row['issue_id']."'>View Incident Movement</a>
                </div>
            </div>
            ".$reqa."
            ".$noc."
            ".$nai."
            ".$comm."
            ".$done."
            ".$media."
            ".$reassign."
            ".$iip."
            ".$acc."
            ".$movement."
            ";
        }
 $so = $row['support_officer'];

    $r = mysqli_fetch_array(mysqli_query($conn, "SELECT * from user where user_id = '$so'"));
    $rr = $r['user_name'];

$assto = $row['user'];
if ($assto == '') {
    $assto = 'No One Yet';
}
$cud = $row['facility'];
$f = mysqli_fetch_array(mysqli_query($conn, "SELECT * from facility where code = '$cud'"));

 $sub_array = array();
 $sub_array[] = $row['issue_id'];
 $sub_array[] = '<div title="'.$f['name'].'">'.$row['facility'].'</div>';
 $sub_array[] = $row['issue_type'];
 $sub_array[] = '<div title="Asigned To: '.$assto.'">'.$row['issue'].'</div>';
 $sub_array[] = $row['priority'];
 $sub_array[] = $rr;
 $sub_array[] = $row['issue_date'];
 $sub_array[] = $actions;
 $sub_array[] = $row['status'];
 
 $data[] = $sub_array;
}

function count_all_data($con, $query)
{
 $statement = $con->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

$output = array(
 "draw"       =>  intval($_POST["draw"]),
 "recordsTotal"   =>  count_all_data($con, $query),
 "recordsFiltered"  =>  $number_filter_row,
 "data"       =>  $data
);

echo json_encode($output);

?>
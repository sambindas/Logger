<?php
session_start();
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

include('../connection.php');
include('../functions.php');
include('c.php');

$column = array('id', 'facility', 'user_id', 'day', 'week', 'date_submitted');

$noww = date('F');
$query = "
SELECT * FROM grn where id > 0
";

if(isset($_POST['search_table']) && $_POST['search_table'] != '')
{
 $query .= ' and 
 (activity like "%'.$_POST['search_table'].'%" or status like "%'.$_POST['search_table'].'%" or facility like "%'.$_POST['search_table'].'%" or pstatus like "%'.$_POST['search_table'].'%" or comments like "%'.$_POST['search_table'].'%")
 ';
}

if ($_POST['datetimepicker1'] != '' || $_POST['datetimepicker2'] != '') {
    $query .= ' and created_at between "'.$_POST['datetimepicker1'].' 00:00:00" and "'.$_POST['datetimepicker2'].' 23:59:59"';
}

if(isset($_POST['order']))
{
 $query .= 'GROUP BY grn_token ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}

else
{
 $query .= 'GROUP BY grn_token ORDER BY id DESC ';
}

$query1 = '';

if($_POST["length"] != -1)
{
 $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

// print_r($query);
// die();
$statement = $con->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $con->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();
$no_id = 1;
foreach($result as $row)
{
$statu = $row['status'];
$user_id = $row['created_by'];
$from = $row['tform'];
$id = $row['id'];
$grn_token = strval($row['grn_token']);
$f = mysqli_query($conn, "SELECT * from locations where code = '".$row['tfrom']."'");
$t = mysqli_query($conn, "SELECT * from locations where code = '".$row['tto']."'");
while ($fr = mysqli_fetch_array($f)) {
    $fro = $fr['name'];
}
while ($tu = mysqli_fetch_array($t)) {
    $tuu = $tu['name'];
}

$u = mysqli_query($conn, "SELECT * from grn_document where grn_token = '$grn_token'");

if (mysqli_num_rows($u) > 0) {
    while ($hm = mysqli_fetch_array($u)) {
        $image = $hm['file_name'];
    }
    $upload = "<a title='View Signed GRN' href='javascript:;' data-featherlight='media/grn/".$image."' data-target='#upload".$grn_token."'><i class='ti-eye'></i><a>
    <a title='Download Signed GRN' href='media/grn/".$image."' download='".$image."'><i class='ti-download'></i><a>";
} else {
    $upload = "<a title='Upload Signed GRN' href='javascript:;' data-toggle='modal' data-target='#upload".$grn_token."'><i class='ti-upload'></i><a>";
}

if ($row['status'] != 0) {

    $r = mysqli_query($conn, "SELECT receive_grn.*, user.user_name from receive_grn inner join user on receive_grn.created_by = user.user_id where grn_token = '$grn_token'");
    $receive_by_array = array();
    while ($rr = mysqli_fetch_array($r)) {
        $receive_by_array[] = $rr['user_name'];
    }
    $r_by = implode('<br>', array_unique($receive_by_array));
   
}
$q1 = mysqli_query($conn, "SELECT * from user where user_id = '$user_id'");
while ($rq1 = mysqli_fetch_array($q1)) {
    $user_name = $rq1['user_name'];
}
if ($statu != 2) {
	$receive = "<a title='Receive Transfer' href='receive.php?token=".$grn_token."'><i class='ti-check-box'></i><a>";
} else {
    $receive = '';
}
if ($statu = 2) {
	$edit = "";
} else {
	$edit = "<a title='Edit Transfer' href='edittransfer.php?token=".$grn_token."'><i class='ti-pencil'></i><a>";
}
if ($_SESSION['id']==7) {
    $delete = "<a title='Delete Transfer' href='transfer.php?token=".$grn_token."'><i class='ti-trash'></i><a>";
}

    
$actions = "<a title='Print Transfer' target='_blank' href='viewtransfer.php?token=".$grn_token."'><i class='ti-receipt'></i><a>
            ".$upload."
            ".$receive."
            ".$edit."
            ".$delete."
            <div class='modal fade' id='upload".$grn_token."'>
                <div class='modal-dialog modal-notify modal-primary modal-notify modal-primary modal-sm'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Upload Signed Transfer</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body'>
                            <form method='post' enctype='multipart/form-data' action=''>
                                <div class='file_upload' id='f1'>
                                    <input name='media' id='profile-img' type='file' required />
                                </div><br>
                                <input name='grn_token' id='' type='hidden' value='".$grn_token."' required />
                                <br><button type='submit' class='btn btn-primary' name='submit_media'>Upload</button>
                            </form><br>
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";

$status = "<span class='badge badge-success'>Initiated by: ".$user_name."</span><br>";
if ($row['status'] == 0) {
    $status .= "<span class='badge badge-danger'>Yet to be received</span>";
} elseif ($row['status'] == 2) {
	$status .= "<span class='badge badge-success'>Received by: ".$r_by."</span>";
} elseif ($row['status'] == 1) {
	$status .= "<span class='badge badge-warning'>Partially Received by: ".$r_by."</span>";
}

 $sub_array = array();
 $sub_array[] = $no_id;
 $sub_array[] = "<a href='javascript:;' onClick='viewItems(\"$grn_token\")'>".$fro."</a>";
 $sub_array[] = $tuu;
 $sub_array[] = date('d-M-Y', strtotime($row['created_at']));
 $sub_array[] = $status;
 $sub_array[] = $actions;
 
 $data[] = $sub_array;
 $no_id++;
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
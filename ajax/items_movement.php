<?php
session_start();
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

include('../connection.php');
include('../functions.php');
include('c.php');

$column = array('id', 'facility', 'user_id', 'day', 'week', 'date_submitted');

$noww = date('F');
$query = "
SELECT * FROM receive_grn where type = 0
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
 $query .= 'GROUP BY receive_token ORDER BY id DESC ';
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
$to = $row['tto'];
$from = $row['tfrom'];
$id = $row['id'];
$receive_token = $row['receive_token'];
$grn_token = strval($row['grn_token']);

if ($row['status'] == 1) {

    $r = mysqli_query($conn, "SELECT receive_grn.*, user.user_name from receive_grn inner join user on receive_grn.approved_by = user.user_id where receive_token = '$receive_token'");
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
if ($statu == 0) {
	$receive = "<a title='Approve' href='approve.php?receive_token=".$row['receive_token']."'><i class='ti-check-box'></i><a>";
} else {
	$receive = '';
}

if ($row['receive_type'] == 0) {
	$type = 'GRN';
} else {
	$type = 'Issue Item';
}

    
$actions = "".$upload."
            ".$receive."
            ".$delete."";

$status = "<span class='badge badge-success'>Initiated by: ".$user_name."</span><br>";
if ($row['status'] == 0) {
    $status .= "<span class='badge badge-danger'>Yet to be received</span>";
} elseif ($row['status'] == 1) {
	$status .= "<span class='badge badge-success'>Received by: ".$r_by."</span>";
}

 $sub_array = array();
 $sub_array[] = $no_id;
 $sub_array[] = $from;
 $sub_array[] = $to;
 $sub_array[] = $type;
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
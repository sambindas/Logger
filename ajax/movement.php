<?php
session_start();
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

include('../connection.php');
include('../functions.php');
include('c.php');

$column = array('id', 'facility', 'user_id', 'day', 'week', 'date_submitted');

$noww = date('F');
$query = "
SELECT * FROM receive_grn where id > 0
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
 $query .= 'GROUP BY receive_token ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
}

else
{
 $query .= 'ORDER BY id DESC ';
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
$user_id = $row['created_by'];
$from = $row['tfrom'];
$to = $row['tto'];
$id = $row['id'];

$receive_token = strval($row['receive_token']);

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

 $sub_array = array();
 $sub_array[] = $no_id;
 $sub_array[] = $row['item_name'];
 $sub_array[] = $from;
 $sub_array[] = $to;
 $sub_array[] = $row['quantity_received'];
 $sub_array[] = date('d-M-Y', strtotime($row['created_at']));
 
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
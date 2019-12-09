<?php
session_start();
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

include('../connection.php');
include('../functions.php');
include('c.php');

$column = array('id', 'facility', 'user_id', 'day', 'week', 'date_submitted');

$noww = date('F');
$query = "
SELECT * FROM items where id > 0
";

if(isset($_POST['search_table']) && $_POST['search_table'] != '')
{
 $query .= ' and 
 (item_name like "%'.$_POST['search_table'].'%" or sku like "%'.$_POST['search_table'].'%")
 ';
}

// if(isset($_POST['order']))
// {
//  $query .= 'GROUP BY grn_token ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
// }

// else
// {
//  $query .= 'GROUP BY grn_token ORDER BY id DESC ';
// }

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

$grn_token = strval($row['grn_token']);

if ($row['status'] != 0) {

    $r = mysqli_query($conn, "SELECT receive_grn.*, user.user_name from receive_grn inner join user on receive_grn.created_by = user.user_id where grn_token = '$grn_token'");
    $receive_by_array = array();
    while ($rr = mysqli_fetch_array($r)) {
        $receive_by_array[] = $rr['user_name'];
    }
    $r_by = implode('<br>', array_unique($receive_by_array));
   
}


$detail = json_decode($row['item_quantity'], true);
$sub_array = array();
$sub_array[] = $no_id;

if (isset($_POST['location']) && $_POST['location'] != '') {
    $rr = $detail[$_POST['location']];
} else {
    $rr = array_sum($detail);
}
foreach ($detail as $ke => $value) {
    $get_name = mysqli_query($conn, "SELECT * from locations where code = '$ke'");
    while ($key_name = mysqli_fetch_array($get_name)) {
        $key = $key_name['name'];
     } 
    $sub_array[] = $row['item_name'];
    
    $sub_array[] = $rr;
}
 
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
<?php
session_start();
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = "activity.php";
include('../connection.php');
include('../functions.php');
include('c.php');

$column = array('id', 'facility', 'user_id', 'day', 'week', 'date_submitted');

$noww = date('F');
$query = "
SELECT * FROM activity 
";

if(isset($_POST['month']) && $_POST['month'] != '')
{
 $query .= ' where 
 month = "'.$_POST['month'].'" 
 ';
} else {
    $query .= "where month = '$noww'";
}

if(isset($_POST['week']) && $_POST['week'] != '')
{
 $query .= ' and 
 week = "'.$_POST['week'].'" 
 ';
}

if(isset($_POST['day']) && $_POST['day'] != '')
{
 $query .= ' and 
 day = "'.$_POST['day'].'" 
 ';
}

if(isset($_POST['logger']) && $_POST['logger'] != '')
{
 $query .= ' and 
 user_id = "'.$_POST['logger'].'" 
 ';
}

if(isset($_POST['search_table']) && $_POST['search_table'] != '')
{
 $query .= ' and 
 (activity like "%'.$_POST['search_table'].'%" or status like "%'.$_POST['search_table'].'%" or facility like "%'.$_POST['search_table'].'%" or pstatus like "%'.$_POST['search_table'].'%" or comments like "%'.$_POST['search_table'].'%")
 ';
}

if(isset($_POST['order']))
{
 $query .= 'GROUP BY user_id ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
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

foreach($result as $row)
{
$status = $row['status'];
$user_id = $row['user_id'];
$id = $row['id'];

$activity_date = $row['activity_date'];
$wik = date('W', strtotime($activity_date));
$q1 = mysqli_query($conn, "SELECT * from user where user_id = '$user_id'");
while ($rq1 = mysqli_fetch_array($q1)) {
    $user_name = $rq1['user_name'];
}

$summary = "<div class='modal fade bd-example-modal-lg' id='osum".$row['id']."'>
                <div class='modal-dialog modal-notify modal-primary modal-lg'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='heading lead'>Other Summary of Week ".$row['week']." by ".$user_name."</h5>
                            <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
                        </div>
                        <div class='modal-body' style='text-align: left;'>
                            
                            <p><i><u>Unplanned Activities:</u></i> ".$row['unplanned']."</p>
                            <p><i><u>Unresolved Incidents:</u></i> ".$row['unresolved']."</p>
                            <p><i><u>Planned Activities (Coming Week):</u></i> ".$row['planned']."</p>
                            <p><i><u>Issues for Management Attention:</u></i> ".$row['issues']."</p>
                            
                        </div>
                        <div class='modal-footer'>
                        </div>
                    </div>
                </div>
            </div>";

$actions = "<div class='dropdown'>
            <button class='btn btn-xs btn-primary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            Action
            </button>
            <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                <a class='dropdown-item' href='addsummary.php?week_token=".$wik."&month=".$row['month']."&week=".$row['week']."&year=".$row['year']."&uid=".$row['user_id']."'>Add Other Summaries For This Week</a>
                <a class='dropdown-item' data-toggle='modal' href='#osum".$row['id']."'>Other Summaries</a>
                <a class='dropdown-item' href='viewactivity.php?month=".$row['month']."&week=".$row['week']."&year=".$row['year']."'>View Week Activity For All Users</a>
            </div>
            </div>".$summary."
            ";

 $sub_array = array();
 $sub_array[] = $row['id'];
 $sub_array[] = $user_name;
 $sub_array[] = $row['month'];
 $sub_array[] = $row['week'];
 $sub_array[] = $row['day'];
 $sub_array[] = $actions;
 
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
<?php
include 'connection.php';

$q = "SELECT * FROM issue where resolution_date IS NULL and (status = 2 or status = 3)";
$qq = mysqli_query($conn, $q);
while ($row = mysqli_fetch_array($qq)) {
    $date = $row['issue_date'];
    $id = $row['issue_id'];
    mysqli_query($conn, "UPDATE issue set resolution_date = '$date' where issue_id = '$id' and (status = 2 or status = 3)");
}
?>
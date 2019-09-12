<?php
session_start();

require '../connection.php';
	
    $so = $_SESSION['name'];
	$issue_id = $_POST['issue_id'];
	$comments = mysqli_real_escape_string($conn, $_POST['comments']);
    $date = date('d-m-Y');

	$query = mysqli_query($conn, "UPDATE issue set status = 1 where issue_id = '$issue_id'");
	$query2 = mysqli_query($conn, "INSERT into comments (issue_id, comment, user, date_added) values ('$issue_id', '$comments', '$so', '$date') ");



		if ($query) {
			echo "1";
		}
		
	 else {
		echo "0";
	}


?>
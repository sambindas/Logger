<?php
session_start();

require '../connection.php';

	$issue_id = $_POST['issue_id'];

	$query = mysqli_query($conn, "SELECT status from issue where issue_id = '$issue_id'");



		if ($row = mysqli_fetch_array($query)) {
			$status = $row['status'];
			if ($status == 0) {
			
			echo "0";
		} else {
			echo "1";
		}
	}
	


?>
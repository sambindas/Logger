<?php
session_start();

require '../connection.php';

	$issue_id = $_POST['issue_id'];

	$query = mysqli_query($conn, "UPDATE issue set status = 2 where issue_id = '$issue_id'");



		if ($query) {
			echo "1";
		}
		
	 else {
		echo "0";
	}


?>
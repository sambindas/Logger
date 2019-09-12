<?php
session_start();

require '../connection.php';


	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$code = mysqli_real_escape_string($conn, $_POST['code']);

	$query = mysqli_query($conn, "INSERT into state (state_name, state_code) 
								values ('$name', '$code')");

	if ($query) {
		$_SESSION['msg'] = '<span class="alert alert-success">State Added Successfully</span>';
		echo "1";
	} else {
		echo "0";
	}

?>
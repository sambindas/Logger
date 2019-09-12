<?php
session_start();

require '../connection.php';

//user role is treated as facility code for clients (e.g EKH, RMC)

	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$code = mysqli_real_escape_string($conn, $_POST['code']);
	$password = sha1($_POST['password']);
	$phone = $_POST['phone'];
	$state = $_POST['state'];
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$date_added = date('l jS \of F Y h:i:s A');

	$query = mysqli_query($conn, "INSERT into user (user_name, user_role, email, password, date_added, user_type, phone, status, state_id) 
								values ('$name', '$code', '$email', '$password', now(), 1, '$phone', 1, '$state')");

	if ($query) {
		$_SESSION['msg'] = '<span class="alert alert-success">Credentials Added Successfully</span>';
		echo "1";
	} else {
		echo "0";
	}

?>
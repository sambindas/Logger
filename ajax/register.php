<?php
session_start();

require '../connection.php';


	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$phone = mysqli_real_escape_string($conn, $_POST['phone']);
	$password = sha1($_POST['password']);
	$token = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz", 5)), 0, 40);
	$role = mysqli_real_escape_string($conn, $_POST['role']);
	$state = $_POST['state'];

	$query = mysqli_query($conn, "INSERT into user (user_name, email, phone, password, date_added, user_role, status, state_id, user_type) 
								values ('$name', '$email', '$phone', '$password', now(), '$role', 1, '$state', 0)");

	if ($query) {
		$_SESSION['msg'] = '<span class="alert alert-success">User Registered Successfully</span>';
		echo "1";
	} else {
		echo "0";
	}

?>
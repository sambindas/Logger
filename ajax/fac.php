<?php
session_start();

require '../connection.php';


	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$code = mysqli_real_escape_string($conn, $_POST['code']);
	$cperson = mysqli_real_escape_string($conn, $_POST['cperson']);
	$cpersonp = mysqli_real_escape_string($conn, $_POST['cpersonp']);
	$serverip = mysqli_real_escape_string($conn, $_POST['serverip']);
	$online_url = mysqli_real_escape_string($conn, $_POST['online_url']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$state = $_POST['state'];

	$query = mysqli_query($conn, "INSERT into facility (name, code, contact_person, contact_person_phone, server_ip, online_url, email, state_id) 
								values ('$name', '$code', '$cperson', '$cpersonp', '$serverip', '$online_url', '$email', $state)");

	if ($query) {
		$_SESSION['msg'] = '<span class="alert alert-success">Facility Added Successfully</span>';
		echo "1";
	} else {
		echo "0";
	}

?>
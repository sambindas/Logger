<?php
session_start();

require '../connection.php';
require '../functions.php';


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

		$subject = 'An account has been created for you';
		$message = 'Hello '.$name.',<br> An account was created for you on eClat Healthcare Incident Log. Find your login details below<br>
	          					<blockquote>
	          					<b>Email:</b> '.$email.'<br>
	          					<b>Password:</b> '.$_POST['password'].'
	          					</blockquote>
	          					Kindly login <a href="incident-log.eclathealthcare.com">here</a> and check.<br> Kind Regards.';

		$send_mail = sendMailUser($email, $name, $subject, $message);

	      if ($send_mail == '0') {
	      	echo "0";
	      } else {
	      	$_SESSION['msg'] = '<span class="alert alert-success">User Registered Successfully and Mail was sent</span>';
			echo "1";
	      }
	}
?>
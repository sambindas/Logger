<?php
session_start();

require '../connection.php';

	$email = $_POST['email'];
	$password = sha1($_POST['password']);
	$user_type = $_POST['user_type'];

		$query = mysqli_query($conn, "SELECT * from user where email = '$email' and password = '$password' and status = 1");

		if ($row = mysqli_fetch_array($query)) {
			if ($row['user_type'] == 0) {
				$_SESSION['name'] = $row['user_name'];
				$_SESSION['email'] = $row['email'];
				$_SESSION['id'] = $row['user_id'];
				$_SESSION['state_id'] = $row['state_id'];
				$_SESSION['logged_user'] = 'support';
				$hid = $_SESSION['id'];
				$query = mysqli_query($conn, "UPDATE user set online = 1 where user_id = '$hid'");
				echo "support";
			} else {
				$_SESSION['name'] = $row['user_name'];
				$_SESSION['email'] = $row['email'];
				$_SESSION['id'] = $row['user_id'];
				$_SESSION['client_code'] = $row['user_role'];
				$_SESSION['state_id'] = $row['state_id'];
				$_SESSION['logged_user'] = 'client';
				$hid = $_SESSION['id'];
				echo "client";
			}
			
		}
		
	 else {
		echo "0";
		}
	


?>
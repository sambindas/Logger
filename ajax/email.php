<?php
session_start();

require '../connection.php';

if (isset($_POST['email'])) {

	$email = $_POST['email'];

	$query = mysqli_query($conn, "SELECT * from user where email = '$email'");

	if (mysqli_num_rows($query) >= 1) {
		echo "1";
	} else {
		echo "0";
	}
}
?>
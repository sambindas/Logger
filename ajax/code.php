<?php
session_start();

require '../connection.php';

if (isset($_POST['code'])) {

	$code = $_POST['code'];

	$query = mysqli_query($conn, "SELECT * from facility where code = '$code'");

	if (mysqli_num_rows($query) >= 1) {
		echo "1";
	} else {
		echo "0";
	}
}
?>
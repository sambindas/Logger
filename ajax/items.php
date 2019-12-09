<?php
session_start();

require '../connection.php';

if (isset($_POST['sku']) and isset($_POST['item_name'])) {
	$sku = $_POST['sku'];
	$item_name = $_POST['item_name'];
	$created_by = $_SESSION['id'];

	$locations = mysqli_query($conn, "SELECT * from locations");
	$locations_array = array();

	if (mysqli_num_rows($locations) > 0) {
		while ($location = mysqli_fetch_array($locations)) {
			$locations_array[$location['code']] = 0;
		}
	}
	$a = json_encode($locations_array);
	$query = mysqli_query($conn, "INSERT into items (item_name, sku, created_by, created_at, item_quantity)
			values ('$item_name', '$sku', '$created_by', now(), '$a')");
	$_SESSION['msg'] = '<span class="alert alert-success">Item Added Successfully</span>';
	if ($query) {
		echo 1;
		exit();
	} else {
		echo 0;
		exit();
	}
}

if (isset($_POST['sku'])) {

	$sku = $_POST['sku'];

	$query = mysqli_query($conn, "SELECT * from items where sku = '$sku'");

	if (mysqli_num_rows($query) >= 1) {
		echo "1";
	} else {
		echo "0";
	}
}

if (isset($_POST['location']) and isset($_POST['state'])) {
	$state = $_POST['state'];
	$location = $_POST['location'];
	$code = $_POST['code'];

	$items = mysqli_query($conn, "SELECT * from items");
	$item_array = array();
	if (mysqli_num_rows($items) > 0) {
			while ($item = mysqli_fetch_array($items)) {
			$items_array_db = json_decode($item['item_quantity'], true);
			$items_array_db[$code] = 0;
			$final = json_encode($items_array_db);
			mysqli_query($conn, "UPDATE items set item_quantity = '$final'");
		}
	}

	$query = mysqli_query($conn, "INSERT into locations (name, state, code)
			values ('$location', '$state', '$code')");
	$_SESSION['msg'] = '<span class="alert alert-success">Location Added Successfully</span>';
	if ($query) {
		echo 1;
	} else {
		echo 0;
	}
}
?>
<?php
session_start();

require '../connection.php';

if(isset($_POST["state"])){
    //Fetch all state data
    $state = $_POST['state'];
    $query = mysqli_query($conn, "SELECT * FROM locations WHERE state = '$state'");
    
    //State option list
    if(mysqli_num_rows($query) > 0){
        echo '<option value="">Select Location</option>';
        while($row = mysqli_fetch_array($query)){ 
            echo '<option value="'.$row['code'].'">'.$row['name'].'</option>';
        }
        exit();
    }else{
        echo '<option value="">Not available</option>';
        exit();
    }
}

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
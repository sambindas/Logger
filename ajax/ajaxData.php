<?php
//Include the database configuration file
include '../connection.php';

if(!empty($_POST["type"])){
    //Fetch all state data
    $type = $_POST['type'];
    $query = mysqli_query($conn, "SELECT * FROM issue_level WHERE type = '$type'");
    
    //State option list
    if(mysqli_num_rows($query) > 0){
        echo '<option value="">Select Level</option>';
        while($row = mysqli_fetch_array($query)){ 
            echo '<option value="'.$row['db_id'].'">'.$row['level'].'</option>';
        }
    }else{
        echo '<option value="">Not available</option>';
    }
}elseif(!empty($_POST["level"])){
    //Fetch all city data
    $level = $_POST['level'];
    $query = mysqli_query($conn, "SELECT * FROM user WHERE status = 1 and user_role = '$level'");
    
    //Count total number of rows
    $rowCount = $query->num_rows;
    
    //City option list
    if(mysqli_num_rows($query) > 0){
        echo '<option value="">Assign</option>';
        while($row = mysqli_fetch_array($query)){ 
            echo '<option value="'.$row['user_name'].'">'.$row['user_name'].'</option>';
        }
    }else{
        echo '<option value="">Not available</option>';
    }
}elseif(!empty($_POST["state"])){
    //Fetch all city data
    $state = $_POST['state'];
    $query = mysqli_query($conn, "SELECT * FROM facility WHERE state_id = '$state'");
    
    //Count total number of rows
    $rowCount = $query->num_rows;
    
    //City option list
    if(mysqli_num_rows($query) > 0){
        echo '<option value="">Select Facility</option>';
        while($row = mysqli_fetch_array($query)){ 
            echo '<option value="'.$row['code'].'">'.$row['name'].'</option>';
        }
    }else{
        echo '<option value="">No Facility in this State</option>';
    }
}
?>
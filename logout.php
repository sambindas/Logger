<?php

session_start();
require 'connection.php';
$hid = $_SESSION['id'];
$query = mysqli_query($conn, "UPDATE user set online = 0 where user_id = '$hid'");
unset($_SESSION["email"]);
unset($_SESSION["name"]);
unset($_SESSION["id"]);
unset($_SESSION["logged_user"]);
unset($_SESSION["state_id"]);
unset($_SESSION["url"]);

header("Location: login.php");



?>
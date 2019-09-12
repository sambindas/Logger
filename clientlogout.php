<?php

session_start();
require 'connection.php';
unset($_SESSION["email"]);
unset($_SESSION["name"]);
unset($_SESSION["id"]);
unset($_SESSION["state_id"]);
unset($_SESSION["client_code"]);
unset($_SESSION["logged_user"]);

header("Location: login.php");



?>
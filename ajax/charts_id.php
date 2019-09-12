<?php
session_start();
error_reporting(0);
include "../connection.php";
if (isset($_POST['type'])) {
    $userid = $_POST['userid'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $status = $_POST['status'];
    $type = $_POST['type'];

    if ($type == 't0') {
        $sql = "SELECT * from issue where support_officer = '$userid' and (status = 0 or status = 8)";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    } elseif ($type == 't5') {
        $sql = "SELECT * from issue where support_officer = '$userid' and status = 5";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    } elseif ($type == 't9') {
        $sql = "SELECT * from issue where support_officer = '$userid' and status = 9";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    } elseif ($type == 't4') {
        $sql = "SELECT * from issue where support_officer = '$userid' and status = 4";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    } elseif ($type == 'td0') {
        $sql = "SELECT * from issue where user = '$userid' and status = 8";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    } elseif ($type == 'td5') {
        $sql = "SELECT * from issue where user = '$userid' and status = 5";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    } elseif ($type == 'td9') {
        $sql = "SELECT * from issue where user = '$userid' and status = 9";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    } elseif ($type == 'td4') {
        $sql = "SELECT * from issue where user = '$userid' and status = 4";
        if ($from != '' and $to != '') {
            $sql .= " and fissue_date between '$from' and '$to'";
        }
    }

    $result = mysqli_query($conn, $sql);

    $response = "<table border='0' width='100%'>";
    while( $row = mysqli_fetch_array($result) ){
    $id = $row['issue_id'];
    $d = $row['issue_date'];
    $date = date('d/m/Y', strtotime($d));
    $time = date("H:i:s",strtotime($d));
    
    $response .= "<tr>";
    $response .= "<td>Incident ID: ".$id." - Logged on ".$date." @ ".$time."</td>";
    $response .= "</tr>";

    }
    $response .= "</table>";

    echo $response;
    exit;
}
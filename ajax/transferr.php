<?php
session_start();
error_reporting(0);
include "../connection.php";
if (isset($_POST['token'])) {
    $token = $_POST['token'];

    if ($token != '') {
        $sql = "SELECT * from grn where grn_token = '$token'";
    }

    $result = mysqli_query($conn, $sql);

    $response = "<table border='0' width='100%'>
                <tr>
                    <th>Item</th>
                    <th>UOM</th>
                    <th>Quantity</th>
                </tr>";
    while($row = mysqli_fetch_array($result)){
    $id = $row['id'];
    $i = $row['item'];
    $s = mysqli_query($conn, "SELECT * from items where sku = '$i'");
    while($ite = mysqli_fetch_array($s)) {
        $item = $ite['item_name'];
    }
    $uom = $row['uom'];
    $quantity = $row['quantity'];
    
    $response .= "<tr>";
    $response .= "<td>".$item."</td>";
    $response .= "<td>".$uom."</td>";
    $response .= "<td>".$quantity."</td>";
    $response .= "</tr>";

    }
    $response .= "</table>";

    echo $response;
    exit;
}
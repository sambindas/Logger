<?php
session_start();
error_reporting(0);
include "../connection.php";
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    if ($id != '') {
        $sql = "SELECT * from locations where id = '$id'";
    }

    $result = mysqli_query($conn, $sql);

    $response = "<table id='location_table' width='100%'>
                <tr>
                    <th>Item</th>
                    <th>Quantity Available</th>
                </tr>";
    while($row = mysqli_fetch_array($result)){
        foreach ($json_decode($row['item']) as $key => $value) {
            # code...
        }
        foreach (json_decode($row['item_quantity']) as $sku => $quantity) {
            
            $s = mysqli_query($conn, "SELECT * from items where sku = '$sku'");
            while($ite = mysqli_fetch_array($s)) {
                $item = $ite['item_name'];
            }
            $response .= "<tr>";
            $response .= "<td>".$item."</td>";
            $response .= "<td>".$quantity."</td>";
            $response .= "</tr>";
        }
    }
    $response .= "</table>";

    echo $response;
    exit;
}
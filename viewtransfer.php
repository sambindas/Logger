<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);
require_once 'vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();

if (!isset($_GET['token'])) {
    header("Location: transfer.php");
}

$token = $_GET['token'];
$sql = mysqli_query($conn, "SELECT * from grn where grn_token = '$token'");
$sql2 = mysqli_query($conn, "SELECT grn.*, items.item_name from grn inner join items on grn.item = items.sku where grn_token = '$token'");
while ($row = mysqli_fetch_array($sql)) {
    $date = $row['created_at'];
    $fromm = $row['tfrom'];
    $too = $row['tto'];
    $n = $row['id'];
    
}
    $f = mysqli_query($conn, "SELECT * from locations where code = '".$fromm."'");
    $t = mysqli_query($conn, "SELECT * from locations where code = '".$too."'");
    while ($fr = mysqli_fetch_array($f)) {
        $from = $fr['name'];
    }
    while ($tu = mysqli_fetch_array($t)) {
        $to = $tu['name'];
    }
while ($r = mysqli_fetch_array($sql2)) {

    $w .= "
    <tr class='item'>
        <td>
            ".$r['item_name']." (".$r['uom'].")
        </td>
        
        <td>
            ".$r['quantity']."
        </td>
    </tr>";
}
$d = date('F d, Y', strtotime($date));

$mpdf->setTitle('Transfer to '.$to.'.');
$mpdf->WriteHTML('<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print GRN</title>
    
    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
        color: #555;
        height: 100%;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="assets/images/logo.jpeg" style="width:35%; max-width:300px;">
                            </td>
                            
                            <td>
                                Transfer #: '.$n.'<br>
                                Created: '.$d.'<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                8, Ikoyi Club Road<br>
                                Lagos
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>
                    Transfer From
                </td>
                
                <td>
                    Transfer To
                </td>
            </tr>
            
            <tr class="details">
                <td>
                    '.$from.'
                </td>
                
                <td>
                    '.$to.'
                </td>
            </tr>
            
            <tr class="heading">
                <td>
                    Item
                </td>
                
                <td>
                    Quantity
                </td>
            </tr>
            '.$w.'
            
            <!-- <tr class="total">
                <td></td>
                
                <td>
                   Total: $385.00
                </td>
            </tr> -->
        </table>
    </div>
</body>
</html>');

$mpdf->Output();

?>
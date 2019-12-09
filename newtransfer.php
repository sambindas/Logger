<?php

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();
if (isset($_POST['submit'])) {
    $tfrom = mysqli_real_escape_string($conn, $_POST['from']);
    $tto = mysqli_real_escape_string($conn, $_POST['to']);
    $date = date('d-m-Y H:i:s');
    $created_by = $_SESSION['id'];
    $pin = mt_rand(100000000, 999999999)
        . mt_rand(100000000, 999999999)
        . $characters[rand(0, strlen($characters) - 1)];
    $grn_token = 'grn-'.str_shuffle($pin);
    for($i = 0; $i < count($_POST['item']); $i++)
    {
        $item = mysqli_real_escape_string($conn, $_POST['item'][$i]);
        $uom = mysqli_real_escape_string($conn, $_POST['uom'][$i]);
        $quantity = mysqli_real_escape_string($conn, $_POST['quantity'][$i]);

        if (empty(trim($item))) continue;

        $sql = "INSERT INTO grn(tfrom, tto, item, uom, quantity, status, created_at, created_by, grn_token, quantity_received)
                VALUES('$tfrom', '$tto', '$item', '$uom', '$quantity', 0, now(), '$created_by', '$grn_token', 0)";
        mysqli_query($conn, $sql);
    }

    if(mysqli_error($connect))
    {
        echo "Data base error occured";
    }
    else
    {
        $_SESSION['msg'] = '<span class="alert alert-success">GRN Initiated Successfully.</span>';
        header('Location: transfer.php');
        exit();
    }
}
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$noww = date('M Y');

$issue_id = $_GET['issue_id'];

if (isset($issue_id)) {
    if (isset($_POST['edit'])) {
        $media_id = $_POST['media_id'];
        $caption = $_POST['caption'];

        mysqli_query($conn, "UPDATE media set caption = '$caption' where media_id = '$media_id'");
        $_SESSION['msg'] = '<span class="alert alert-success">Media Caption Edited Successfully.</span>';
    }
    if (isset($_POST['delete'])) {
        $media_id = $_POST['media_id'];

        mysqli_query($conn, "DELETE from media where media_id = '$media_id'");
        $_SESSION['msg'] = '<span class="alert alert-success">Media Deleted Successfully.</span>';
    }
}

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Create Transfer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <!-- amcharts css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="jquery.datetimepicker.css">
<script type="text/javascript" src="assets/ckeditor/ckeditor.js"></script>
    <!-- modernizr css -->
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body id="mybody">
            <?php
            require 'sidebar.php';
            require 'header.php';
            ?>
                    <div class="container">
                                <?php 
                                if (isset($_SESSION['msg'])) {
                                    echo $_SESSION['msg'];
                                    unset($_SESSION['msg']);
                                }
                                ?>
                                <div class="crcform">
                                    <h4>Initiate Transfer</h4>
                                    <form action="" method="post">
                                        <table class="table table-bordered" id="dynamic_field">
                                            <tr>
                                                <td>
                                                    <div class="form-row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom01">Transferring From</label>
                                                            <select name="state" id="state" class="cus custom-select border-0 pr-3" required>
                                                                <option value="" selected="">Select Item</option>
                                                                <?php
                                                                $fc = mysqli_query($conn, "SELECT * from state order by state_name asc");
                                                                while ($fc_row = mysqli_fetch_array($fc)) {
                                                                    echo '<option value="'.$fc_row['state_name'].'">'.$fc_row['state_name'].'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom02">Location</label>
                                                            <select name="from" id="location" class="cus custom-select border-0 pr-3" required>
                                                                <option value="" selected="">Select State First</option>
                                                            </select>
                                                        </div>
                                                    </div><hr>
                                                    <div class="form-row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom01">Transferring To</label>
                                                            <select name="state" id="tstate" class="sele custom-select border-0 pr-3" required>
                                                                <option value="" selected="">Select Item</option>
                                                                <?php
                                                                $fc = mysqli_query($conn, "SELECT * from state order by state_name asc");
                                                                while ($fc_row = mysqli_fetch_array($fc)) {
                                                                    echo '<option value="'.$fc_row['state_name'].'">'.$fc_row['state_name'].'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom02">Location</label>
                                                            <select name="to" id="tlocation" class="sele custom-select border-0 pr-3" required>
                                                                <option value="" selected="">Select State First</option>
                                                            </select>
                                                        </div>
                                                    </div><hr>

                                                    <div class="form-row field_wrapper">
                                                        <div class="col-md-6 mb-3">
                                                            <select name="item[]" class="sel custom-select border-0 pr-3" required>
                                                                <option value="" selected="">Select Item</option>
                                                                <?php
                                                                $fc = mysqli_query($conn, "SELECT * from items");
                                                                while ($fc_row = mysqli_fetch_array($fc)) {
                                                                    echo '<option value="'.$fc_row['sku'].'">'.$fc_row['item_name'].'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 mb-3">
                                                            <input type="text" class="form-control" name="uom[]" id="validationCustom04" placeholder="UOM" required="">
                                                        </div>
                                                        <div class="col-md-2 mb-3">
                                                            <input type="text" class="form-control" name="quantity[]" id="validationCustom05" placeholder="Quantity" required="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>
                                            </tr>
                                        </table>
                                        <input type="submit" name="submit" id="submit"  class="btn btn-info" value="Submit" />
                                    </form>
                                </div>
                                <br>
                    </div>
                </div>
            </div>
        </div>
        <!-- main content area end -->
        <!-- footer area start-->
        <footer>
            <div class="footer-area">
                <p>© Copyright 2019. All right reserved.</p>
            </div>
        </footer>
        <!-- footer area end-->
    </div>
    <!-- page container area end -->
    <!-- jquery latest version -->
    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet"/>

    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <!-- others plugins -->
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="jquery.datetimepicker.full.min.js"></script>
    <script src="jquery.datetimepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function(){
            var i = 1;
            $('#add').click(function(){
                i++;
                $('#dynamic_field').append('<tr id="row'+i+'"><td><div class="form-row field_wrapper"><div class="col-md-6 mb-3"><select name="item[]" class="sele custom-select border-0 pr-3" required><option value="" selected="">Select Item</option><?php $fc = mysqli_query($conn, "SELECT * from items"); while ($fc_row = mysqli_fetch_array($fc)) {echo '<option value="'.$fc_row['sku'].'">'.$fc_row['item_name'].'</option>';}?></select></div><div class="col-md-2 mb-3"><input type="text" class="form-control" id="validationCustom04" placeholder="UOM" name="uom[]" required=""></div><div class="col-md-2 mb-3"><input type="text" class="form-control" name="quantity[]" placeholder="Quantity" required=""></div></div></td></td><td><button name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
            });

            $(document).on('click','.btn_remove', function(){
                var button_id = $(this).attr("id");
                $("#row"+button_id+"").remove();
            });

            $('#submit').click(function(){
                $.ajax({
                    async: true,
                    url: "internship_details.php",
                    method: "POST",
                    data: $('#internship_details').serialize(),
                    success:function(rt)
                    {
                        alert(rt);
                        $('#internship_details')[0].reset();
                    }
                });
            });

            $('#tstate').on('change',function(){
                var state = $(this).val();
                if(state){
                    $.ajax({
                        type:'POST',
                        url:'ajax/state.php',
                        data:'state='+state,
                        success:function(html){
                            $('#tlocation').html(html);
                        }
                    }); 
                }else{
                    $('#level').html('<option value="">Select Type first</option>');
                    $('#assign').html('<option value="">Select Level first</option>'); 
                }
            });

            $('#state').on('change',function(){
                var state = $(this).val();
                if(state){
                    $.ajax({
                        type:'POST',
                        url:'ajax/state.php',
                        data:'state='+state,
                        success:function(html){
                            $('#location').html(html);
                        }
                    }); 
                }else{
                    $('#level').html('<option value="">Select Type first</option>');
                    $('#assign').html('<option value="">Select Level first</option>'); 
                }
            });
        });
    </script>
    <script type="text/javascript">
        // $(document).ready(function() {
        //     $(document).ready(function() {
        //         $('.sele').select2();
        //     });
        //     $(document).ready(function() {
        //         $('.cus').select2();
        //     });
        // });
    </script>
</html>
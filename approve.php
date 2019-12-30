<?php

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();

if (isset($_GET['receive_token'])) {
    $receive_token = $_GET['receive_token'];
    $g = mysqli_query($conn, "SELECT * from receive_grn where receive_token = '$receive_token' and status = 0");
    $g2 = mysqli_query($conn, "SELECT * from receive_grn where receive_token = '$receive_token' and status = 0");

    while($gr = mysqli_fetch_array($g)){
        $from = $gr['tfrom'];
        $to = $gr['tto'];
    }
}

if (isset($_POST['process_grn'])) {
    $created_by = $_SESSION['id'];
    $from = mysqli_real_escape_string($conn, $_POST['from']);
    $to = mysqli_real_escape_string($conn, $_POST['to']);
    $receive_token = $_POST['receive_token'];

    if ($_POST['receive_type'] == 1) {
        for($i = 0; $i < count($_POST['item']); $i++) {
            $item = $_POST['item'][$i];
            $quantity = $_POST['quantity'][$i];

            $location_fr = mysqli_query($conn, "SELECT * from locations where name = '$from'");
            while($ll = mysqli_fetch_array($location_fr)){
                $code = $ll['code'];
            }

            $location_from = mysqli_query($conn, "SELECT * from items where item_name = '$item'");
                
            #add quantity to location sending to
            while ($location_from_d = mysqli_fetch_array($location_from)) {

                $location_from_db = json_decode($location_from_d['item_quantity'], true);
                if (array_key_exists($code, $location_from_db)) {
                    foreach ($location_from_db as $key => $value) {
                        if ($code == $key) {
                            $new_value = $value - $quantity;
                            $location_from_db[$key] = $new_value;
                            $updated_array = json_encode($location_from_db);
                            $u = mysqli_query($conn, "UPDATE items set item_quantity = '$updated_array' where item_name = '$item'");
                        }
                    }
                }
            }
        }
    } elseif ($_POST['receive_type'] == 0) {
        for($i = 0; $i < count($_POST['item']); $i++) {
            $item = $_POST['item'][$i];
            $quantity = $_POST['quantity'][$i];

            $location_to = mysqli_query($conn, "SELECT * from locations where name = '$to'");
            while($ll = mysqli_fetch_array($location_to)){
                $code = $ll['code'];
            }

            $location_from = mysqli_query($conn, "SELECT * from items where item_name = '$item'");
                
            #add quantity to location sending to
            while ($location_from_d = mysqli_fetch_array($location_from)) {

                $location_from_db = json_decode($location_from_d['item_quantity'], true);
                if (array_key_exists($code, $location_from_db)) {
                    foreach ($location_from_db as $key => $value) {
                        if ($code == $key) {
                            $new_value = $value + $quantity;
                            $location_from_db[$key] = $new_value;
                            $updated_array = json_encode($location_from_db);
                            $u = mysqli_query($conn, "UPDATE items set item_quantity = '$updated_array' where item_name = '$item'");
                        }
                    }
                }
            }
        }
    }

    $status_c = mysqli_query($conn, "UPDATE receive_grn set status = 1, approved_by = ".$_SESSION['id']." where receive_token = '$receive_token'");
    $_SESSION['msg'] = '<span class="alert alert-success">Approved Successfully and Quantities balanced.</span>';
    header('Location: item_movement.php');
    exit();
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Receive Items</title>
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
                                    <h4>Approve Items</h4>
                                    <form action="" method="post">
                                        <table class="table table-bordered" id="dynamic_field">
                                            <tr>
                                                <td>
                                                    <div class="form-row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom01">Transferring From</label>
                                                            <input type="text" class="form-control" name="" value="<?php echo $from; ?>" readonly>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="validationCustom02">Transferring To</label>
                                                            <input type="text" class="form-control" name="" value="<?php echo $to; ?>" readonly>
                                                        </div>
                                                    </div><hr>
                                                    <?php
                                                    while ($grn = mysqli_fetch_array($g2)) {
                                                        $name = $grn['item_name'];
                                                        $qty = $grn['quantity_received'];
                                                    ?>
                                                    <div class="form-row field_wrapper">
                                                        <div class="col-md-4 mb-8">
                                                            <input type="text" class="form-control" name="item[]" readonly value="<?php echo $name; ?>" required="">
                                                        </div>
                                                        <input type="hidden" name="receive_token" value="<?php echo $grn['receive_token']; ?>">
                                                        <input type="hidden" name="grn_id[]" value="<?php echo $grn['id']; ?>">
                                                        <input type="hidden" name="receive_type" value="<?php echo $grn['receive_type']; ?>">
                                                        <input type="hidden" name="from" value="<?php echo $grn['tfrom']; ?>">
                                                        <input type="hidden" name="to" value="<?php echo $grn['tto']; ?>">
                                                        <div class="col-md-2 mb-2">
                                                            <input type="text" class="form-control" name="quantity[]" value="<?php echo $qty; ?>" placeholder="Quantity Received" required="">
                                                        </div>
                                                        <!-- <div class="col-md-2 mb-2">
                                                            <input type="checkbox" class="" name="quantity">
                                                        </div> -->
                                                    </div>
                                                    <?php } ?>
                                            </tr>
                                        </table>
                                        <input type="submit" name="process_grn" id="submit"  class="btn btn-info" value="Process" />
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
                $('#dynamic_field').append('<tr id="row'+i+'"><td><div class="form-row field_wrapper"><div class="col-md-6 mb-3"><select name="item[]" class="selec custom-select border-0 pr-3" required><option value="" selected="">Select Item</option><?php $fc = mysqli_query($conn, "SELECT * from items"); while ($fc_row = mysqli_fetch_array($fc)) {echo '<option value="'.$fc_row['sku'].'">'.$fc_row['item_name'].'</option>';}?></select></div><div class="col-md-2 mb-3"><input type="text" class="form-control" placeholder="UOM" name="uom[]" required=""></div><div class="col-md-2 mb-3"><input type="text" class="form-control" name="quantity[]" placeholder="Quantity" required=""></div></div></td></td><td><button name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
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
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).ready(function() {
                $('.selec').select2();
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).ready(function() {
                $('.sele').select2();
            });
        });
    </script>

</html>
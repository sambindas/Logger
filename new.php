<?php

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();
if ($_SESSION['logged_user'] == 'client') {
    header('Location: clientindex.php');
}

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$noww = date('M Y');

$state_id = $_SESSION['state_id'];
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Add an Incident</title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6/css/select2.min.css" rel="stylesheet"/>
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
                        <div class="">
                            <form method="post" action="processing.php" name="issue_form" id="issue_form" enctype="multipart/form-data">
                                <div class="login-form-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Facility</h4>
                                                <select name="facility" class="custom-select border-0 pr-3" required>
                                                    <option value="" selected="">Select One</option>
                                                    <?php
                                                    $fc = mysqli_query($conn, "SELECT * from facility where state_id = $state_id");
                                                    while ($fc_row = mysqli_fetch_array($fc)) {
                                                        echo '<option value="'.$fc_row['code'].'">'.$fc_row['name'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Type</h4>
                                                <select name = "type" id="type" class="custom-select border-0 pr-3" required>
                                                    <option value="" selected="">Select One</option>
                                                    <option value="Issue">Issue</option>
                                                    <option value="Request">Request</option>
                                                    <option value="Other">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-gp">
                                                <h4  class="header-title mb-0">Incident Level</h4>
                                                <select name="il" id="level" class="custom-select border-0 pr-3" required>
                                                    <option value="" selected="">Select Type First</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-gp">
                                                <h4  class="header-title mb-0">Assign To</h4>
                                                <select name="assign" id="assign" class="custom-select border-0 pr-3">
                                                    <option value="">Select Level First</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="header-title mb-0">Incident</h4>
                                    <textarea cols="73" rows="6" type="text" id="issue" name="issue" placeholder="issue" required></textarea>
                                    <script>
                                        CKEDITOR.replace( 'issue' );
                                    </script><br>
                                    <div class="row"> 
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Incident Client Reporter</h4>
                                                <input type="text" name="icr" id="icr" required>
                                            </div>
                                        </div>
                                        <input type="hidden" name="url" value="<?php echo $url; ?>">
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Affected Department(<span style="text-transform: lowercase;">s<span>)</h4>
                                                <input type="text" name="ad" id="ad" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Priority</h4>
                                                <select name="priority" class="custom-select border-0 pr-3" required>
                                                    <option value="" selected="">Select One</option>
                                                    <option value="High">High</option>
                                                    <option value="Medium">Medium</option>
                                                    <option value="Low">Low</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">           
                                            <div class="form-gp">
                                                <h4 class="header-title mb-0">Incident Reported On</h4>
                                                <input type="text" id="datetimepicker" name="iro" required>
                                            </div>
                                        </div>
                                        <input type="hidden" name="post_type" value="0">
                                    </div>
                                </div>
                                <input type="Submit" name="submit_issue" value="Submit Incident" style="float: right;" class="btn btn-primary">
                            </form>
                        </div>
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

    <script type="text/javascript">
        $(document).ready(function(){
            jQuery('#datetimepicker').datetimepicker();
            $('#filters').click(function(){
                    $('#filterid').toggle();
            });
            jQuery('#datetimepicker1').datetimepicker({
             i18n:{
              de:{
               months:[
                'January','February','March','April',
                'May','June','July','August',
                'September','October','November','December',
               ],
               dayOfWeek:[
                "Su.", "Mo", "Tu", "We", 
                "Th", "Fr", "Sa.",
               ]
              }
             },
             timepicker:false,
             format:'d/m/Y'
            });
        });

         $('#submit_issue').click(function(){
            var issue = $('#issue').val();

            if (issue == '') {
                alert('Please Fill the issue');
                return false;
            }
         });
    </script>
    <script type="text/javascript">
    $(document).ready(function(){
    $('#type').on('change',function(){
        var type = $(this).val();
        if(type){
            $.ajax({
                type:'POST',
                url:'ajax/ajaxData.php',
                data:'type='+type,
                success:function(html){
                    $('#level').html(html);
                    $('#assign').html('<option value="">Select Level first</option>'); 
                }
            }); 
        }else{
            $('#level').html('<option value="">Select Type first</option>');
            $('#assign').html('<option value="">Select Level first</option>'); 
        }
    });

    $('#level').on('change',function(){
        var level = $(this).val();
        if (level == 1) {
            var levell = 'Support Officer';
        } else if (level == 2) {
            var levell = 'Developer';
        } else if (level == 3) {
            var levell = 'Developer';
        } else if (level == 4) {
            var levell = 'Developer';
        }
        if(levell){
            $.ajax({
                type:'POST',
                url:'ajax/ajaxData.php',
                data:'level='+levell,
                success:function(html){
                    $('#assign').html(html);
                }
            }); 
        }else{
            $('#assign').html('<option value="">Select Level first</option>'); 
        }
    });
    });
    </script>
    <script>
        $(document).ready(function(){
            $('#assign').on('change',function(){
                var drop = $('#smail');
                if (drop.val() == '') {
                    drop.hide();
                } else {
                    drop.show();
                }
            });
        });
    </script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#rotimi').select2();
        });
    </script>

</html>
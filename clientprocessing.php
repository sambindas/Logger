<?php

session_start();
require 'connection.php';
require 'functions.php';
checkUserSession();

if (isset($_POST['submit_issue'])) {
	$facility = $_SESSION['client_code'];
    $type = $_POST['type'];
    $issue = $_POST['issue'];
    $icr = $_SESSION['name'];
    $iro = $_POST['iro'];
    $irod = date('d-m-Y @ H:i:s', strtotime($iro));
    $ad = $_POST['ad'];
    $so = $_SESSION['id'];
    $priority = $_POST['priority'];
    $date = date('d-m-Y H:i:s');
    $fdate = date('Y-m-d');
    $url = "clientindex.php";
    $month = date('M Y');
    $son = $_SESSION['name'];
    $post_type = $_POST['post_type'];
    $state_id = $_SESSION['state_id'];
    $user = 'Technical Support';

        $insert = mysqli_query($conn, "INSERT INTO issue (state_id, facility, issue_type, issue_level, issue, issue_date, fissue_date, issue_client_reporter, affected_dept, support_officer, priority, status, month, issue_reported_on, user, type)
         VALUES ('$state_id', '$facility', '$type', 5, '$issue', '$date', '$fdate', '$icr', '$ad', '$so', '$priority', 0, '$month', '$irod', '$user', '$post_type')");
        $last_id = mysqli_insert_id($conn);
        
        if ($insert) {
            $log = mysqli_query($conn, "INSERT into movement (issue_id, done_by, done_at, movement) values ('$last_id', '$so', '$date', 'Incident was submitted')");
        } else {
            $_SESSION['msg'] = '<span class="alert alert-danger">Incident Not Submitted. An Error Occured.</span>';
            header("Location: clientindex.php ");
        exit();
        }
            mysqli_query($conn, "UPDATE issue set status = 8 where issue_id = '$last_id'");
                $u = mysqli_query($conn, "SELECT * from user where user_name = '$user'");
                while ($rr = mysqli_fetch_array($u)) {
                    $email = $rr['email'];
                }
                $rrr = strtok($user, " ");
                $msg = '<span class="alert alert-success">Incident Submitted Successfully and mail sent to <span style="text-transform: lowercase;">'.$email.'.</span></span>';

                $subject = 'An Incident From A Client Has Been Assigned To You';
                $message = 'Hello '.$rrr.' <br> Incident Log S/N '.$last_id.' from a client has been assigned to you by '.$son.'. 
                <br>
                <blockquote><b>Client</b>: '.$facility.'<br><b>Details</b>: '.$issue.'</blockquote>
                <br>
                Please <a href="incident-log.eclathealthcare.com">Log In</a> and Check. <br> Best Regards.';
                sendMail($email, $rrr, $subject, $message, $msg, $url);

        $_SESSION['msg'] = '<span class="alert alert-success">Incident Submitted Successfully.</span>';
        header("Location: clientindex.php ");

    }

if (isset($_POST['edit_issue'])) {
    $facility = $_POST['facility'];
    $type = $_POST['type'];
    $il = $_POST['il'];
    $issue = $_POST['issue'];
    $issue_id = $_POST['issue_id'];
    $icr = $_POST['icr'];
    $iro = $_POST['iro'];
    $irod = date('d-m-Y @ H:i:s', strtotime($iro));
    $ad = $_POST['ad'];
    $so = $_SESSION['id'];
    $priority = $_POST['priority'];
    $date = date('d-m-Y H:i:s');
    $url = $_POST['url'];
    $action = "Incident was Edited";

        $insert = mysqli_query($conn, "UPDATE issue set facility = '$facility', issue_type = '$type', issue_level = '$il', issue = '$issue', issue_date = '$date',
         issue_client_reporter = '$icr', affected_dept = '$ad', support_officer = '$so', priority = '$priority', issue_reported_on = '$irod' where issue_id = '$issue_id'");
        if ($insert) {
            $log = mysqli_query($conn, "INSERT into movement (issue_id, done_by, done_at, movement) values ('$issue_id', '$so', '$date', '$action')");
        }
        if ($log) {

        $_SESSION['msg'] = '<span class="alert alert-success">Incident Edited Successfully.</span>';
        header("Location: index.php ");
}
    }

?>
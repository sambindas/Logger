<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

require 'connection.php';

$previous_week = strtotime("-1 week +1 day");

$start_week = strtotime("last monday midnight",$previous_week);
$end_week = strtotime("next sunday",$start_week);

$start_week = date("Y/m/d",$start_week);
$end_week = date("Y/m/d",$end_week);

$user_table_email = array();
$activity_table_email = array();

$users = mysqli_query($conn, "SELECT user_name, email from user where user_type = 0 and status = 1 and user_role = 'Support Officer'");
while ($user = mysqli_fetch_array($users)) {
	$user_table_email[] = $user['user_name'];
  $user_email[] = $user['email'];
}
// print_r($user_table_email);

$email_activity = mysqli_query($conn, "SELECT * from email_activity where send_email = 1");
while ($email = mysqli_fetch_array($email_activity)) {
  if ($email['type'] == 'Activity') {
    $send_email[] = $email['email'];
  } elseif ($email['type'] == 'Issues') {
    $send_issues[] = $email['email'];
  } else {
    $send_issues[] = $email['email'];
    $send_email[] = $email['email'];
  }
}

// print_r($send_email);
$mess2 = 'Hello, <br>
These are the Issues for Management Attention for the previous week. ';
$attn = mysqli_query($conn, "SELECT activity.issues, activity.user_id, user.user_name, activity.facility from activity inner join user on activity.user_id = user.user_id where activity.issues IS NOT NULL and activity_date between '$start_week' and '$end_week' group by activity.issues");
while ($attention = mysqli_fetch_array($attn)) {
	$attention_user[] = $attention['user_name'];
  $attention_issue[] = $attention['issues'];
  $attention_facility[] = $attention['facility'];

  $mess2 .= '
			<br><blockquote>'.$attention['user_name'].$attention['issues'].'</blockquote>';
}
$mess2 .= 'Kind Regards';

$a_users = mysqli_query($conn, "SELECT count(activity.user_id) as user_count, activity.user_id, user.user_name from activity inner join user on activity.user_id = user.user_id  where activity_date between '$start_week' and '$end_week' group by activity.user_id");
while ($a_user = mysqli_fetch_array($a_users)) {
	if ($a_user['user_count'] >= 5) {
		$activity_table_email[] = $a_user['user_name'];
	}
}
// print_r($activity_table_email);

$diff = array_diff($user_table_email, $activity_table_email);
// print_r($diff);
// die();
foreach ($diff as $email_key) {
  $users_email = mysqli_query($conn, "SELECT email from user where user_name = '$email_key'");
    while ($user_email = mysqli_fetch_array($users_email)) {
    $users_table_email[] = $user_email['email'];
  }
}

if (empty($diff)) {
	$mess = 'There were no Incomplete Logs this week';
} else {
	$mess = 'Hello, <br>
			The following Support Officers have Incomplete Reporting Activity For the previous week. 
			<br><blockquote>'.implode("<br>", $diff).'</blockquote><br>
			Kind Regards';
}


      //Instantiation and passing `true` enables exceptions
      if (isset($send_email)){
        $mail = new PHPMailer(true);
        try {
          //Server settings
          $mail->SMTPDebug = 2;                                       // Enable verbose debug output
          $mail->isSMTP();                                            // Set mailer to use SMTP
          $mail->Host       = 'smtp.gmail.com;';  // Specify main and backup SMTP servers
          $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
          $mail->Username   = 'incidentlog00@gmail.com';              // SMTP username
          $mail->Password   = 'wallace@femi';                         // SMTP password
          $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
          $mail->Port       = 587;                                    // TCP port to connect to

          //Recipients
          $mail->setFrom('incidentlog00@gmail.com', 'Incident Log');
          $mail->addReplyTo('incidentlog00@gmail.com', 'Incident Log');
          while (list ($key, $val) = each ($send_email)) {
    			$mail->AddAddress($val);
    			}
          while (list ($key, $va) = each ($users_table_email)) {
          $mail->addCC($va);
          }

          // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = 'Incomplete Activity Log';
          $mail->Body    = $mess;
          

          $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
      }

      if (isset($send_issues)){
        $mail2 = new PHPMailer(true);
      
        try {
            //Server settings
            $mail2->SMTPDebug = 2;                                       // Enable verbose debug output
            $mail2->isSMTP();                                            // Set mailer to use SMTP
            $mail2->Host       = 'smtp.gmail.com;';  // Specify main and backup SMTP servers
            $mail2->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail2->Username   = 'incidentlog00@gmail.com';              // SMTP username
            $mail2->Password   = 'wallace@femi';                         // SMTP password
            $mail2->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
            $mail2->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail2->setFrom('incidentlog00@gmail.com', 'Incident Log');
            $mail2->addReplyTo('incidentlog00@gmail.com', 'Incident Log');
            while (list ($keyy, $vall) = each ($send_issues)) {
            $mail2->AddAddress($vall);
            }

            // Content
            $mail2->isHTML(true);                                  // Set email format to HTML
            $mail2->Subject = 'Issues For Management Attention';
            $mail2->Body    = $mess2;
            

            $mail2->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail2->ErrorInfo}";
        }
      }

      if (isset($send_email) and isset($send_issues)){
        $mail = new PHPMailer(true);
        $mail = new PHPMailer(true);

        try {
          //Server settings
          $mail->SMTPDebug = 2;                                       // Enable verbose debug output
          $mail->isSMTP();                                            // Set mailer to use SMTP
          $mail->Host       = 'smtp.gmail.com;';  // Specify main and backup SMTP servers
          $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
          $mail->Username   = 'incidentlog00@gmail.com';              // SMTP username
          $mail->Password   = 'wallace@femi';                         // SMTP password
          $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
          $mail->Port       = 587;                                    // TCP port to connect to

          //Recipients
          $mail->setFrom('incidentlog00@gmail.com', 'Incident Log');
          $mail->addReplyTo('incidentlog00@gmail.com', 'Incident Log');
          while (list ($key, $val) = each ($send_email)) {
    			$mail->AddAddress($val);
    			}
          while (list ($key, $va) = each ($users_table_email)) {
          $mail->addCC($va);
          }

          // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = 'Incomplete Activity Log';
          $mail->Body    = $mess;
          

          $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        try {
          //Server settings
          $mail2->SMTPDebug = 2;                                       // Enable verbose debug output
          $mail2->isSMTP();                                            // Set mailer to use SMTP
          $mail2->Host       = 'smtp.gmail.com;';  // Specify main and backup SMTP servers
          $mail2->SMTPAuth   = true;                                   // Enable SMTP authentication
          $mail2->Username   = 'incidentlog00@gmail.com';              // SMTP username
          $mail2->Password   = 'wallace@femi';                         // SMTP password
          $mail2->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
          $mail2->Port       = 587;                                    // TCP port to connect to

          //Recipients
          $mail2->setFrom('incidentlog00@gmail.com', 'Incident Log');
          $mail2->addReplyTo('incidentlog00@gmail.com', 'Incident Log');
          while (list ($keyy, $vall) = each ($send_issues)) {
          $mail2->AddAddress($vall);
          }

          // Content
          $mail2->isHTML(true);                                  // Set email format to HTML
          $mail2->Subject = 'Issues For Management Attention';
          $mail2->Body    = $mess2;
          

          $mail2->send();
      } catch (Exception $e) {
          echo "Message could not be sent. Mailer Error: {$mail2->ErrorInfo}";
      }
      }


?>
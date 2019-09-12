<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exceptoin;
$conn = mysqli_connect("localhost", "root", "", "laundry");
error_reporting(0);

function checkUserSession() {
    if (!isset($_SESSION['logged_user'])) {
        header("Location: login.php");
    } else {
        $email = $_SESSION['email'];
        $name = $_SESSION['name'];
    }
}

$uid = $_SESSION['id'];
$s = mysqli_query($conn, "SELECT * from user where user_id='$uid'");
while ($ss = mysqli_fetch_array($s)) {
   $user_state_id = $ss['state_id'];
   $l = mysqli_query($conn, "SELECT * from state where id='$user_state_id'");
    while ($l_row = mysqli_fetch_array($l)) { $state_name = $l_row['state_name']; }
}

$hid = $_SESSION['id'];
$timee = time();

$tim = $timee+300;

$update = mysqli_query($conn, "UPDATE user set online_status = '$tim' where user_id = '$hid'");

function sendMail($email, $rrr, $subject, $message, $msg, $url) {
  // Load Composer's autoloader
      require 'vendor/autoload.php';

      // Instantiation and passing `true` enables exceptions
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
          $mail->addAddress($email, $rrr);     // Add a recipient
          $mail->addReplyTo('incidentlog00@gmail.com', 'Incident Log');

          // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = $subject;
          $mail->Body    = $message;
          

          $mail->send();
          $_SESSION['msg'] = $msg;
          return 1;
          exit();
      } catch (Exception $e) {
          echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
}

function sendMail2($email, $rrr, $subject, $message, $msg, $url, $email2) {
  // Load Composer's autoloader
      require 'vendor/autoload.php';

      // Instantiation and passing `true` enables exceptions
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
          $mail->addAddress($email);     // Add a recipient
          $mail->addAddress($email2);
          $mail->addReplyTo('incidentlog00@gmail.com', 'Incident Log');

          // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = $subject;
          $mail->Body    = $message;
          

          $mail->send();
          $_SESSION['msg'] = $msg;
          return 1;
          exit();
      } catch (Exception $e) {
          echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
}

function secondsToTime($inputSeconds) {
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;

    // Extract days
    $days = floor($inputSeconds / $secondsInADay);

    // Extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // Extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // Extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // Format and return
    $timeParts = [];
    $sections = [
        'day' => (int)$days,
        'hour' => (int)$hours,
        'minute' => (int)$minutes,
        'second' => (int)$seconds,
    ];

    foreach ($sections as $name => $value){
        if ($value > 0){
            $timeParts[] = $value. ' '.$name.($value == 1 ? '' : 's');
        }
    }

    return implode(', ', $timeParts);
}

function make_query($conn, $issue_id)
{

$conn = mysqli_connect("localhost", "root", "", "laundry");
$query = "SELECT * FROM media where issue_id = '$issue_id'";
$result = mysqli_query($conn, $query);
 return $result;
}

function make_slide_indicators($conn, $issue_id)

{
 $output = ''; 
 $count = 0;
 $result = make_query($conn, $issue_id);
 while($row = mysqli_fetch_array($result))
 {
  if($count == 0)
  {
   $output .= '
   <li data-target="#dynamic_slide_show" data-slide-to="'.$count.'" class="active"></li>
   ';
  }
  else
  {
   $output .= '
   <li data-target="#dynamic_slide_show" data-slide-to="'.$count.'"></li>
   ';
  }
  $count = $count + 1;
 }
 return $output;
}

function make_slides($conn, $issue_id)
{
 $output = '';
 $count = 0;
 $result = make_query($conn, $issue_id);
 if (mysqli_num_rows($result) > 0 ) {
 while($row = mysqli_fetch_array($result))
 {
  if($count == 0)
  {
   $output .= '<div class="item active">';
  }
  else
  {
   $output .= '<div class="item">';
  }
  $output .= '
   <a href="media/'.$row["media_name"].'" class="gallery"><img class="fancybox" src="media/'.$row["media_name"].'"  alt="'.$row["caption"].'"/></a>
   <div class="carousel-caption">
   </div>
  </div>
  ';
  $count = $count + 1;
 }
  return $output;
} else {
    $output = '<p>No Media For This Incident!</p>';
 return $output;
}
}
?>
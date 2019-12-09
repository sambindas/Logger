<?php
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
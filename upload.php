<?php
include 'connection.php';
if(isset($_POST["submit_file"]))
{
 $file = $_FILES["file"]["tmp_name"];
 $file_open = fopen($file,"r");
 while(($csv = fgetcsv($file_open, 1000, ",")) !== false)
 {
  $facility = $csv[0];
  $type = $csv[1];
  $il = $csv[2];
  $issue = mysqli_real_escape_string($conn, $csv[3]);
  $date = $csv[4];
  $fdate = $csv[5];
  $icr = $csv[6];
  $ad = $csv[7];
  $so = $csv[8];
  $priority = $csv[9];
  $status = $csv[10];
  $month = $csv[11];
  $irod = $csv[4];
  $irb = $csv[12];
  $rd = $csv[13];
  $irt = $csv[14];
  $im = $csv[15];

  $insert = mysqli_query($conn, "INSERT INTO issue (facility, issue_type, issue_level, issue, issue_date, fissue_date, issue_reported_on, issue_client_reporter, affected_dept, support_officer, priority, status, month, resolved_by, resolution_date, info_relayed_to, info_medium)
         VALUES ('$facility', '$type', '$il', '$issue', '$date', '$fdate', '$irod', '$icr', '$ad', '$so', '$priority', '$status', '$month', '$irb', '$rd', '$irt', '$im')");
  	
 }

 if ($insert) {
       $_SESSION['msg'] = '<span class="alert alert-success">Issues Imported Successfully.</span>';
        header("Location: index.php");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Upload</title>
</head>
<body>
<h1>MAKE SURE YOUR EXCEL FILE IS SAVED AS .CSV</h1>
<form action="" method="post" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" name="submit_file" value="submit" required>
</form>
</body>
</html>
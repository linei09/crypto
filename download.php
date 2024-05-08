<?php   session_start();  ?>

<!DOCTYPE html>
<html>
<head>
    
  <title>Upload and Download File</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<?php
      if(!isset($_SESSION['use'])) // If session is not set then redirect to Login Page
       {
           header("Location:login.php");  
       }
       if(!isset($_SESSION['user'])) // If session is not set then redirect to Login Page
       {
           header("Location:login.php");  
       }
          echo "\t Welcome ";
          echo $_SESSION['user'];

          echo ", Login Success";

          echo "<a href='logout.php'> Logout</a> "; 
          if (isset($_POST['back']))
{
    header("Location:preindex.php");
}
?>
<style>
.form{
width: 100%;
display: inline-block;
position: inherit;
padding: 6px;
}

.label {
padding: 10px;
width: 10%;
}
.input{
position: inherit;
padding: 3px;
margin-left: 2.3%;
}

.btn{
margin-left: 6.5%;
background-color: blue;
color: white;
}
</style>
<?php
    require 'vendor/autoload.php';
    use Google\Cloud\Storage\StorageClient;
$con = mysqli_connect("localhost","root","","dbupload");
$user1 = $_SESSION['use'];
if (mysqli_connect_errno()) {
echo "Unable to connect to MySQL! ". mysqli_connect_error();
}
if (isset($_POST['back']))
{
header("Location:preindex.php");
}
if (isset($_POST['download']))
{
    $fname = $_POST['filename'];
            //"SELECT * FROM `tblfiles`"
    $sql3 = "SELECT * FROM `tblfiles` WHERE `FileName` = `$fname`";
    $res3 = mysqli_query($con, "SELECT * FROM tblfiles WHERE FileName = '$fname'", MYSQLI_STORE_RESULT);
    $row3 = mysqli_fetch_array($res3, MYSQLI_BOTH);
    $fname = $row3["Location"];
    $filename = $row3["FileName"];
    $position = $row3["POSITION"];
    $department = $row3["MAPHONG"];
    $data = [
        'FileName' => $fname,
        'MAPHONG' => $department,
        'POSITION' => $position,
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.137.19:5000/api/download');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl,CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $respond = curl_exec($curl);
    curl_close($curl);
    echo $respond;
if ($respond === false) {
// Handle error
echo 'Error accessing the API';
exit();
} else {
            // Process the response
            $data = json_decode($respond, true);
    
            // Access specific values
            $key = $data['key'];
// Handle success
$storage = new StorageClient([
        
    'keyFilePath' => 'proud-portfolio-389210-03e0ae91957b.json'
]);
$bucket = $storage->bucket('cryptography-project');
$object = $bucket->object($fname);
$object->downloadToFile($fname);

// Decryption settings
//$key = base64_decode('A56j88DZi3rClw3yJikQxEDjuo+NaTMuVthIW6m6XGg='); // Replace with your own decryption key

// Read the encrypted data from the file
$encryptedData = file_get_contents($fname);
$key = base64_decode($key);
// Extract the IV, tag, and ciphertext
$iv = substr($encryptedData, 0, 16);
$tag = substr($encryptedData, 16, 16);
$cipherText = substr($encryptedData, 32);

// Decrypt the file
$originalFileName = str_replace('encrypt_', '', $fname);
$decryptedFile = 'decrypt_' . $originalFileName;
$plainText = openssl_decrypt($cipherText, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

// Write the decrypted data to a file
file_put_contents($decryptedFile, $plainText);
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($decryptedFile) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($decryptedFile));
readfile($decryptedFile);
exit;

echo 'File decrypted successfully!';
}

}   
?>
<center>
<h1>Upload and Download</h1>
<form class="form" method="post" action="" enctype="multipart/form-data">
<label class="label">File Download</label>
<input type="text" name="filename" class="input" placeholder="Enter File Name">
<button type="submit" name="download" class="btn"><i class="fa fa-download fw-fa" ></i>Download</button>
<button type="submit" name="back" class="btn"><i class="fa fa-upload fw-fa" ></i>Go Back</button>
</form>
</center>
<br>
<div class="container">
<table id="demo" class="table table-bordered">
<thead>
<tr>
<td>STT</td>
<td>Tên File </td>
</tr>
</thead>
<tbody>
<?php
$sqli = "SELECT * FROM `tblfiles`";
$res = mysqli_query($con, $sqli);
$id = 1;
while ($row = mysqli_fetch_array($res)) {
echo '<tr>';
echo '<td>'.$id.'</td>';
echo '<td>'.$row['FileName'].'</td>';
echo '</tr>';
$id++;
}
mysqli_close($con);
?>
</tbody>
</table>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript">
</script>
</body>
</html>
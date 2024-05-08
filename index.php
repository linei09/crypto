<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload and Download File</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<?php
if (!isset($_SESSION['use'])) // If session is not set then redirect to Login Page
{
    header("Location:login.php");
}
if (!isset($_SESSION['user'])) // If session is not set then redirect to Login Page
{
    header("Location:login.php");
}
echo "\t Welcome ";
echo $_SESSION['user'] . PHP_EOL ;

echo "Login Success";

echo "<a href='logout.php'> Logout</a> ";
?>
<style>
    .form {
        width: 100%;
        display: inline-block;
        position: inherit;
        padding: 6px;
    }

    .label {
        padding: 10px;
        width: 10%;
    }

    .input {
        position: inherit;
        padding: 3px;
        margin-left: 2.3%;
    }

    .btn {
        margin-left: 6.5%;
        background-color: blue;
        color: white;
    }
</style>
<?php
require 'vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;

$con = mysqli_connect("localhost", "root", "", "dbupload");
$user1 = $_SESSION['use'];
if (mysqli_connect_errno()) {
    echo "Unable to connect to MySQL! " . mysqli_connect_error();
}
if (isset($_POST['back']))
{
    header("Location:preindex.php");
}
if (isset($_POST['save'])) {
    
    $filename = $_POST['filename'];
    $maphong = $_POST['MAPHONG'];
    $position = $_POST['POSITION'];
            $data = [
                'FileName' => $filename,
                'MAPHONG' => $maphong,
                'POSITION' => $position,
            ];
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'http://192.168.137.19:5000/api/key');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl,CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $respond = curl_exec($curl);
            curl_close($curl);
    if ($respond === false) {
        // Handle error
        echo 'Error accessing the API';
        exit();
    } else {
        // Process the response
        $data = json_decode($respond, true);
    
        // Access specific values
        $key = $data['key'];
        $target_dir = "Uploaded_Files/";
    $target_file =date("dmYhis") . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

    if ($imageFileType != "jpg" || $imageFileType != "png" || $imageFileType != "jpeg" || $imageFileType != "gif") {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $files = date("dmYhis") . basename($_FILES["file"]["name"]);

            // Encryption settings
            //$key = random_bytes(32); // Generate a random encryption key
            $key = base64_decode($key);
            $iv = openssl_random_pseudo_bytes(16); // Generate a random IV
            $tag = null; // Will be populated after encryption

            // Encrypt the file
            $encryptedFile = 'encrypt_' . $files;
            $cipherText = openssl_encrypt(file_get_contents($target_file), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

            // Write the encrypted data to a file
            file_put_contents($encryptedFile, $iv . $tag . $cipherText); 
            $storage = new StorageClient([
                'keyFilePath' => 'proud-portfolio-389210-03e0ae91957b.json',
            ]);
         
            $bucketName = 'cryptography-project';
            $bucket = $storage->bucket($bucketName);
            $filename1 = $encryptedFile;
            $vitri = $encryptedFile;
            $object = $bucket->upload(
                fopen($filename1, 'r')
            );
            //public file after upload
            $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);
            unlink($target_file); // Remove the original unencrypted file
            unlink($encryptedFile); // Remove the encrypted file

        } else {
            echo "Error Uploading File";
            
            exit;
        }
    } else {
        echo "File Not Supported";
        
        exit;
    }
    $location = $encryptedFile;
    $duplicate = mysqli_query($con, "select * from tblfiles where FileName='$filename'");
    if (mysqli_num_rows($duplicate) > 0) {
        echo "File Name Already Exists";
        
    } else {
        $sqli = "INSERT INTO tblfiles (MANV, MAPHONG, POSITION, FileName, Location) VALUES ('$user1','{$maphong}','{$position}','{$filename}','{$vitri}')";
        $result = mysqli_query($con, $sqli);
        //convert $key to string
        if ($result) {
            echo "File has been uploaded";
        };
    }
    }
}
?>
<center>
    <h1>Upload and Download</h1>
    <form class="form" method="post" action="" enctype="multipart/form-data">
        <label>Filename:</label>
        <input type="text" name="filename"> <br/>
        <div style="margin-left: 9%">
            <label>File:</label>
            <input type="file" name="file"> <br/>
        </div>
        <div style="margin-left: -9%">
            <label>Department: </label>
            <select name="MAPHONG">
                <?php
                $con = mysqli_connect("localhost", "root", "", "dbupload");
                $sql = "SELECT * FROM `phong`";
                $res = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_array($res)) {
                    echo '<option value="' . $row['MAPHONG'] . '">' . $row['MAPHONG'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div style="margin-left: -8%">
            <label>Position: </label>
            <select name="POSITION">
                <?php
                $con = mysqli_connect("localhost", "root", "", "dbupload");
                $sql = "SELECT DISTINCT POSITION FROM `nhanvien`";
                $res = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_array($res)) {
                    echo '<option value="' . $row['POSITION'] . '">' . $row['POSITION'] . '</option>';
                }
                ?>
            </select>
        </div>
        <button type="submit" name="back" class="btn"><i class="fa fa-upload fw-fa" style="margin-left: 5%"></i>Back</button>
        <button type="submit" name="save" class="btn" id="upload"><i class="fa fa-upload fw-fa"></i> Upload</button>

    </form>
</center>
<br>
<div class="container">
    <table id="demo" class="table table-bordered">
        <thead>
        <tr>
            <td>Mã NV</td>
            <td>Tên File</td>
            <td>Mã Phòng</td>
            <td>Vị Trí</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $sqli = "SELECT * FROM `tblfiles` where `MANV` = '$user1' order by `id` DESC";
        $res = mysqli_query($con, $sqli);
        while ($row = mysqli_fetch_array($res)) {
            echo '<tr>';
            echo '<td>' . $row['MANV'] . '</td>';
            echo '<td>' . $row['FileName'] . '</td>';
            echo '<td>' . $row['MAPHONG'] . '</td>';
            echo '<td>' . $row['POSITION'] . '</td>';
            echo '</tr>';
        }
        mysqli_close($con);
        ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<script type="text/javascript">
</script>
</body>
</html>

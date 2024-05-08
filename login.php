<?php  session_start();   // session starts with the help of this function 

if(isset($_SESSION['use']))   // Checking whether the session is already there or not if 
                              // true then header redirect it to the home page directly 
 {
    header("Location:index.php"); 
 }

 
if(isset($_POST['login']))   // it checks whether the user clicked login button or not 
{
     $userid = $_POST['userid'];
     $user = $_POST['user'];
     $pass = $_POST['pass'];
     $department = $_POST['department'];
     $position = $_POST['position'];
     $conn = mysqli_connect("localhost","root","","dbupload");
     $sql = "select * from `nhanvien` where USERNAME = '$user' and pass = '$pass' and MANV = '$userid' and MAPHONG = '$department' and POSITION = '$position'";
     $res = mysqli_query($conn,$sql);
     $count = mysqli_num_rows($res);
     echo $count;
      if($count)  // username is  set to "Ank"  and Password   
         {                                   // is 1234 by default     

          $_SESSION['use']=$userid;
          $_SESSION['user']=$user;


         echo '<script type="text/javascript"> window.open("preindex.php","_self");</script>';            //  On Successful Login redirects to home.php

        }

        else
        {
            echo "invalid UserName or Password";        
        }
}
 ?>
<html>
<head>

<title> Login Page   </title>

</head>

<body>

<form action="" method="post">

    <table width="200" border="0">
  <tr>
    <td> MANV  </td>
    <td><input type="text" name="userid"></td>
  </tr>
  <tr>
    <td>  Username</td>
    <td> <input type="text" name="user" > </td>
  </tr>
  <tr>
    <td> Password  </td>
    <td><input type="password" name="pass"></td>
  </tr>
  <tr>
    <td> Department  </td>
    <td><input type="text" name="department"></td>
  </tr>
  <tr>
    <td> Position  </td>
    <td><input type="text" name="position"></td>
  </tr>
  <tr>
    <td> <input type="submit" name="login" value="LOGIN"></td>
    <td></td>
  </tr>
</table>
</form>

</body>
</html>
<?php
session_start();
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

echo ", Login Success ";
 
echo "<a href='logout.php'>Logout</a> "; 
if(isset($_POST['upload']))
{
    header("Location:index.php");
}
if(isset($_POST['download']))
{
    header("Location:download.php");
}
?>



<html>
<head>
<title> Upload/Download Page   </title>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript">
</script>
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
</head>
<body>
<form action="" method="post">
    <br>
    <button type="submit" name="upload" class="btn"><i class="fa fa-upload fw-fa"></i> Upload Page</button>
    <br><br>
    <button type="submit" name="download" class="btn"><i class="fa fa-upload fw-fa"></i> Download Page</button>
</html>

<!DOCTYPE HTML>
<html>
<head>
    <title>Admin</title>
   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../css/bootstrap.css" rel="stylesheet"> 
</head>

<body>



<?php 
include("../blocks/bd.php");
require_once '../blocks/testField.php';
session_start();


 
 if(isset($_POST['login'])){$login=$_POST['login'];}else{unset($login);}
 if(isset($_POST['pass'])){$pass=$_POST['pass'];}else{unset($pass);}

 $test = new testField();

$united = [];

if($test->forname($login)){array_push($united, $login);}else{unset($login);}
if($test->forname($pass)){array_push($united, $pass);}else{unset($pass);}
 

if(isset($login) && isset($pass)){

  $result=$pdo->query("SELECT pass FROM regis WHERE user='$login'");
  $myrow= $result->fetch();

 if(!$myrow)
  {
    header('Location: ../../index.html');
    
    exit();
  }
  
    if($pass===0)
  {
    header('Location: ../../index.html');
    exit();
  }
  
      if($pass!=$myrow['pass'])
  {
    header('Location: ../../index.html');
    exit();
  }
  
  if($pass==$myrow['pass'])
  {
    $_SESSION['Name']=$login;
    header('Location: content.php');
  }else{
   header('Location: ../../index.html');
  }


   
  
  

}else{
    header('Location: ../../index.html');
    
    exit();
  }







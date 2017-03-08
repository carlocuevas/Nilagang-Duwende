<?php
require 'dbconfig.php';
session_start();
//Query For Login
	if (isset($_POST['SignIn']))
	{
		$Uname=$_POST['Un'];
		$Pword=$_POST['Pword'];

	

		if($sql=mysql_query("SELECT * from tbluser where UserName='".$Uname."' And Password='".$Pword."' And UserType='Cashier'"))
	        {
	            $count=mysql_num_rows($sql);
	            $row=mysql_fetch_array($sql);
	            if($count>0)
	            {   
	            	
	 		           	$_SESSION = array();
	            		$_SESSION['UserID']=$row['UserName'];
	            		$_SESSION['U_ID']=$row['UserId'];
	            	
						echo '<script type="text/javascript"> alert("Login Cashier"); </script>';
	                	header("location:POS.php");
	            }
	            elseif($sql1=mysql_query("SELECT * from tbluser where UserName='".$Uname."' And Password='".$Pword."' And UserType='Admin'"))
				       	{
				            $count1=mysql_num_rows($sql1);
				            $row1=mysql_fetch_array($sql1);
				            
				            if($count1>0)
				            {   
				              	$_SESSION = array();
			            		$_SESSION['UserID']=$row['UserName'];
			            		$_SESSION['U_ID']=$row['UserId'];
				                echo '<script type="text/javascript"> alert("Login Admin"); </script>';
				                header("location:Inventory.php");

				            }
				                else
				            {
				                echo'<script>alert("wrong username and password");</script>';

				            }
			             
	       				}  
	        }
	 
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Dwarf's Pharmacy</title>
	<meta charset="UTF-8">
	<link rel="icon" href="images/icon.png" type="image/png" sizes="16x16">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="Medicine,Tablets,Affordable">
	<meta name="description" content="Nilagang Duwende,Dwarfs Pharmacy">
	<meta name="author" content="nilagang duwende">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/LoginUI.css">
	<script src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
<style type="text/css">
	body
	{
		background-color:#DADFE1;
		font-family: "Century Gothic";
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
	    $("#interface").fadeIn(2000);
	 	document.getElementById("Un").focus();
	});
</script>
</head>

<body>
<nav class="navbar navbar-inverse"style="background:#e74c3c;border-color:#e74c3c;">
  <div class="container-fluid" >
    <div class="navbar-header" >
      <img src="images/GayMon.JPG" class="logo" alt=""style="float:left; width:55px; height: 50px; padding: 5px;margin-left:-10px;"><span class="navbar-brand" style="font-size:40px;" >Dwarf's Pharmacy</span>
    </div>
  </div>
</nav>
<div id="UI">
	<div id="interface">
		<div id="texxt">
			<span style="margin-left:10px;">Log In</span>
		</div>
		<div id="Login">
		<form method="POST" action="">
			<div id="Inputs">
					<div class="form-group has-feedback">
					    <input type="text" class="form-control" name="Un" placeholder="User Name" />
					    <span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
					    <input type="password" class="form-control" placeholder="Password" name="Pword"/>
					    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
			</div>
			<button class="btn btn-danger" name="SignIn" style="border-radius:0px;">Sign In</button>
		</form>
		</div>	

	</div>
</div>
<footer id="footer">	
	<marquee>Copyright @2015 Nilagang Duwende's Department</marquee>
</footer>

</body>
</html>
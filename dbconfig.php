<?php
$host="localhost";
$database="dwarfph";
$username="root";
$password='';
$conn= mysql_connect($host,$username,$password);
$db=mysql_select_db($database,$conn);
?>
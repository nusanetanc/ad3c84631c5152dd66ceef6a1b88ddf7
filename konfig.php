<?php
$name="localhost";
$user="nusanet-jkt";
$pass="Hmxd8R6FbKdrqfMT";
$db="blastgroovy";
$connection = mysql_connect($name,$user,$pass) or die(mysql_error());
mysql_select_db($db,$connection) or die(mysql_error());
?>

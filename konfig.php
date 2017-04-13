<?php
$name="localhost";
$user="blastgroovy";
$pass="dwRvwy7BsSb8HRdX";
$db="blastgroovy";
$connection = mysql_connect($name,$user,$pass) or die(mysql_error());
mysql_select_db($db,$connection) or die(mysql_error());
?>

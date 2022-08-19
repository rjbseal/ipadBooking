<?php
//**********************************************
//*
//*  Connect to database
//*
//**********************************************

$servername = "localhost";
$username = "root";
$password = "";
$dbName = "computer_bookings";
$errorMsg = mysqli_connect_error();

$conn = mysqli_connect( $servername, $username, $password, $dbName );

if (!$conn)
{
	echo "<h1>Unable to connect to MySQL: ".mysqli_error()."<br>".$errorMsg."</h1>";
}

?>
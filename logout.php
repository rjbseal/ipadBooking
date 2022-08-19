<?php
session_start();

// Destroys all sessions out and redirects to login page

if(session_destroy())
{
	header("Location: login.php");
}
?>
<style>

body {
  color:#333333;
  font-family:Helvetica, Arial, sans-serif;
  line-height:20px;
}
</style>

<?php

include 'dbConnect.php';
include 'default_timezone.php';

// select current number of ipads/laptops in use at the specified date/time
// and the number of staff members using them
$bookingQuantity = 10;
$bookingDate = "2016/01/13";
$bookingTime = "09:00";
$returnTime  = "09:30"; 

$sql  = "SELECT bookingID, COUNT(DISTINCT staffID) AS numOfStaff, SUM(bookingQuantity) AS iPadsInUse, ";
$sql .= "bookingDateTime, returnDateTime ";
$sql .= "FROM bookings ";
$sql .= "WHERE DATE(bookingDateTime) = '$bookingDate' ";
$sql .= "AND TIME(bookingDateTime) BETWEEN '$bookingTime' AND '$returnTime'";

// this checks whether there are any iPads booked out before our booking time and have not yet been returned
$sql .= "OR DATE(bookingDateTime)  = '$bookingDate' "; 
$sql .= "AND TIME(bookingDateTime) <= '$bookingTime' ";
$sql .= "AND TIME(returnDateTime) >= '$returnTime' ";

// this checks if there are any bookings between our booking and return times
$sql .= "OR DATE(bookingDateTime)  = '$bookingDate' "; 
$sql .= "AND TIME(returnDateTime) BETWEEN '$bookingTime' AND '$returnTime'" ;  // are there any iPads in use between 17:30 and 18:30?
$sql .= "AND bookingCategory = 'iPad'"; // will change to dynamic value at later date

$result = mysqli_query($conn, $sql);

$msg = ""; // this will populate with info about the num of ipads in use and by who at the current time

if ($result)
{
	$numRows = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	
	$numOfStaff		 = $row['numOfStaff'];
	$iPadsInStock    = 10; 
	$iPadsInUse      = $row['iPadsInUse'];
	$iPadsAvbl       = $iPadsInStock - $iPadsInUse;
	$bookingID       = $row['bookingID'];
	
	/*$msg  = "There are <b>".$iPadsInUse."</b> iPads in use during these times. <br />";
	$msg .= "<b>".$bookingDate." ".$bookingTime."-".$returnTime."</b><br />";
	$msg .= "iPads available: <b>".$iPadsAvbl."</b>";
	*/
		
	if ($bookingQuantity > $iPadsAvbl)
	{
		$msg  .= "There are not enough iPads in stock. <p>";
		$msg .= "Booking Quantity: <b>".$bookingQuantity."</b><br />";
		$msg .= "iPads in use on ".$bookingDate." between ". $bookingTime." and ". $returnTime.": <b>".$iPadsInUse."</b><br />";
		$msg .= "Number of staff that have booked out: <b>".$numOfStaff."</b><br />";
		$msg .= "iPads available: <b>".$iPadsAvbl."</b>";
	} else {
		$msg .= "Booking confirmed. <p>";
		$msg .= "Booking Quantity: <b>".$bookingQuantity."</b><br />";
		$msg .= "iPads in use on ".$bookingDate." between ". $bookingTime." and ". $returnTime.": <b>".$iPadsInUse."</b><br />";
		$msg .= "Number of staff that have booked out: <b>".$numOfStaff."</b><br />";
		$msg .= "iPads available: <b>".$iPadsAvbl."</b>";
	}	
}

echo $msg;
?>
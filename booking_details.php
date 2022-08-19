<html>
<head>

	<title>Booking Details</title>
	<link rel="stylesheet" href="css/bookingform.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	
	<!-- script file containing live date/time feed -->
	<script src="scripts/dateTime.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<script>
    var auto_refresh = setInterval(function () {
    $('.View').fadeOut('slow', function() {
        $(this).load('/echo/json/', function() {
            $(this).fadeIn('slow');
        });
    });
}, 15000); // argh! why doesn't this work!!!
</script>
 
</head>

	
	<body onload="startTime()">
	<!-- div for displaying live date/time -->
	<b><div align="center" id="datetime"></div></b><p>
	
<div class="" align="center">
<?php

include 'dbConnect.php';
include 'default_timezone.php';

// select current number of ipads/laptops in use at the moment
// and the number of staff members using them

$sql  = "SELECT COUNT(DISTINCT staffID) AS numOfStaff, SUM(bookingQuantity) AS ipadsInUse, ";
$sql .= "bookingDateTime, returnDateTime ";
$sql .= "FROM bookings ";
$sql .= "WHERE DATE(bookingDateTime) = CURDATE() ";
$sql .= "AND TIME(bookingDateTime) < CURTIME() ";
$sql .= "AND TIME(returnDateTime)  > CURTIME() ";
$sql .= "AND bookingCategory = 'iPad'"; // will change to dynamic value at later date

 /* // SELECT the number of iPads that will be returned next

$sqlReturnAmount  = "SELECT bookingQuantity, bookingDateTime, returnDateTime ";
$sqlReturnAmount .= "FROM bookings ";
$sqlReturnAmount .= "WHERE DATE(bookingDateTime) = CURDATE() AND TIME(bookingDateTime) < CURTIME() AND TIME(returnDateTime) > CURTIME() ";
$sqlReturnAmount .= "ORDER BY returnTime ASC";
*/

/* // Determine how many bookings are scheduled for the current date

$sqlNextBooking  = "SELECT TIME(bookingDateTime) ";
$sqlNextBooking .= "FROM bookings ";
$sqlNextBooking .= "WHERE DATE(bookingDateTime) = CURDATE() AND TIME(bookingDateTime) > CURTIME()";
*/

$result = mysqli_query($conn, $sql);

$msg = ""; // this will populate with info about the num of ipads in use and by who at the current time

if ($result)
{
	$row = mysqli_fetch_assoc($result);
	
	$iPadsInStock     = 10; 			   // the total number of iPads that are in stock
	$numOfStaff = $row['numOfStaff'];	   // the total number of staff that are currently using iPads 
	$ipadsInUse = $row['ipadsInUse'];      // the total number of iPads that are currently in use
	$iPadsAvbl  = $iPadsInStock - $ipadsInUse; // the number of iPads that are currently available
	
	if ($ipadsInUse == 0)
	{
		$msg  = "Number of iPads available: <b>".$iPadsAvbl."/".$iPadsInStock."</b><br />";
		$msg .= "There are no iPads in use at the moment.";
	} else {
		$msg  = "Number of iPads available: <b>".$iPadsAvbl."/".$iPadsInStock."</b><br />";
		$msg .= "There are <b>".$ipadsInUse."</b> iPads currently booked out by <b>".$numOfStaff."</b> member(s) of staff.";
	}
}

echo $msg;
?>

</div>
</body>
</html>
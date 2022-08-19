<?php
	session_start();
	
	// if a session exists	
	if(isset($_SESSION['username']))
	{	
		// display a welcome message for the currently logged in user	
		$msg = "You are logged in as ".$_SESSION['username'];
	} else {
		
		// no session exists/user has not logged in...redirect to login page
		header("Location: login.php"); 
	}
?>

<html>
<head>
	<title>All Bookings</title>
	
	<link rel="stylesheet" href="css/displaybookings.css">
	<!-- script file containing live date/time feed -->
	<script src="scripts/dateTime.js"></script>

	<script>
	function reloadPage()
	{
		location.reload();
	}
	</script>
	
</head>


<body onload="startTime()">
<!-- div container that runs along the very top of the page for holding login and date/time info -->
<div class="infobar"> 
<b><?php echo $msg." ID: ".$_SESSION['staffID']; ?></b>. <a href="logout.php">Logout</a>

<!-- div for displaying live date/time -->
<div id="datetime"></div>
</div>
<form id="myform" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

<?php

include 'dbConnect.php';

//**********************************************
//*
//*  SELECT bookings from database
//*
//**********************************************

	$sql_select  = "SELECT bookings.bookingID, staff.staffID, staff.staffFirstName, staff.staffLastName, ";
	$sql_select .= "bookings.bookingCategory, bookings.bookingQuantity, ";
	$sql_select .= "DATE(bookings.bookingDateTime) AS bookingDate,";
	$sql_select .= "TIME(bookings.bookingDateTime) AS bookingTime, ";
	$sql_select .= "TIME(bookings.returnDateTime)  AS returnTime ";
	$sql_select .= "FROM bookings ";
	$sql_select .= "INNER JOIN staff ";
	$sql_select .= "ON bookings.staffID=staff.staffID ";
	//$sql_select .= "WHERE DATE(bookingDateTime) = CURDATE() ";
	$sql_select .= "ORDER BY bookings.bookingDateTime DESC";

	$result = mysqli_query($conn, $sql_select);

if ($result)
{
	$numRows = mysqli_num_rows($result);
	echo "<div style='width:800px; margin:0 auto;'>";
	echo "<table>";
	echo "<tr><th>Staff</th>";
	echo "<th>Category</th>";
	echo "<th>Quantity</th>";
	echo "<th>Booking Date</th>";
	echo "<th>Booking Time</th>";
	echo "<th>Return Time</th>";
	echo "<th>Delete</th></tr>";
	
	$color  = "";
	$color1 = "#e5e5ff";
	$color2 = "#FFFFF";
	
	for ($i = 0; $i < $numRows; $i ++) 
	{		
		if ($color == $color2)
		{
			$color = $color1;
		} else {
			$color = $color2;
		}
		
		$row = mysqli_fetch_assoc($result);
		
		$bookingID       = $row['bookingID'];
		$firstName       = $row['staffFirstName'];
		$lastName        = $row['staffLastName'];
		$bookingCategory = $row['bookingCategory'];
		$bookingQuantity = $row['bookingQuantity'];
		$bookingDate     = $row['bookingDate'];
		$bookingTime     = $row['bookingTime'];
		$returnTime		 = $row['returnTime'];
		
		if (isset($_POST[$bookingID]))
		{
			$checked = $_POST[$bookingID];
		} else {
			$checked = 'N';
		}
		
		if ($checked == 'Y')
		{
			deleteBooking($conn, $bookingID, $bookingDate, $bookingTime);
		} else {
			
			echo "<tr style='background-color:$color'>";
			echo "<td>".$firstName." ".$lastName."</td>";
			echo "<td>".$bookingCategory."</td>";
			echo "<td>".$bookingQuantity."</td>";
			echo "<td>".date("d-m-Y", strtotime($bookingDate))."</td>\n";
			echo "<td>".date('H:i', strtotime($bookingTime))."</td>\n";
			echo "<td>".date('H:i', strtotime($returnTime))."</td>\n";
			echo "<td><input type='checkbox' name='".$bookingID."' value='Y'></td>";
			echo "</tr>\n";
		}	
	}
	
	echo "</table>";
	
}

echo "<br><input type='submit' value='Delete Booking' />";

?>
</form>
	<button onclick="reloadPage()">Reload page</button>
	<a href='make_booking.php'>Back</a>
</div>
</body>
</html>

<?php

function deleteBooking($conn, $bookingID, $bookingDate, $bookingTime)
{
	$delete_statement 	= "DELETE FROM bookings ";
	$delete_statement  .= "WHERE bookingID = '".$bookingID."' ";

	$result = mysqli_query($conn, $delete_statement);

	if ($result)
	{
		echo "<p style='color: green;'>Booking ID: ".$bookingID.", ".$bookingDate. " deleted.";
	} else {
		$outputDisplay .= "<p style='color: red;'>MySQL No: ".mysqli_errno($conn)."<br>";
		$outputDisplay .= "MySQL Error: ".mysqli_error($conn)."<br>";
		$outputDisplay .= "<br>SQL: ".$sql_select."<br>";
		$outputDisplay .= "<br>MySQL Affected Rows: ".mysqli_affected_rows($conn)."</font><br>";
	}
}

?>
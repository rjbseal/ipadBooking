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
	<title>Make a Booking</title>
	
	<link rel="stylesheet" href="css/bookingform.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	
	<!-- script file containing live date/time feed -->
	<script src="scripts/dateTime.js"></script>
	
	<!-- script files containing calendar gui for booking date field -->
	<script src="scripts/datepicker.js"></script>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		$(function()
		{
			$( "#datepicker" ).datepicker({ dateFormat: 'yy/mm/dd', minDate: 0 }); // 'minDate: 0' prevents selecting in past 
		});
  </script>
</head>

<body onload="startTime()">

<?php 

include 'dbConnect.php'; // database connection file
include 'default_timezone.php'; // sets timezone to UK to allow for correct booking times

$errMsg = "";
$successMsg = "";
$errDivDisplay = "none";
$successDivDisplay = "none";

// if the user has clicked the submit button, begin validation checks

if (isset($_POST['submitBtn']))
{	
	// check the booking date field is not empty
	
	if (empty($_POST['bookingDate']))
	{
		$errMsg = "You must select a booking date.";
		$errDivDisplay = "block";
	} else {
		
		// store user values
		
		$staffID	     = $_SESSION['staffID'];
		
		$bookingCategory = $_POST['bookingCategory'];
		$bookingQuantity = $_POST['bookingQuantity'];
		$bookingDate     = $_POST['bookingDate'];
		$bookingTime     = $_POST['bookingTimeHour'].":".$_POST['bookingTimeMinute'];
		$bookingDateTime = date('Y-m-d H:i', strtotime($bookingDate.$bookingTime));
		
		$returnDate     = $bookingDate; // staff must return iPads on same day, variable is for database purposes only
		$returnTime     = $_POST['returnTimeHour'].":".$_POST['returnTimeMinute'];
		$returnDateTime = date('Y-m-d H:i', strtotime($returnDate.$returnTime));

		// check to make sure booking is not made in the past 
		// and that the return time is not before the booking time
		
		if(strtotime($bookingDateTime) < time() || strtotime($returnDateTime) < time() 
			||  strtotime($returnDateTime) <  strtotime($bookingDateTime))
		{
			$errMsg = "You cannot book in the past!";
			$errDivDisplay = "block";
		} else {
			
			//*******************************************
			//*
			//* 	Check that there are iPads available 
			//*		at the given date/time and that there
			//*		are no booking conflicts
			//*
			//*******************************************
			
			$sql  = "SELECT COUNT(DISTINCT staffID) AS numOfStaff, SUM(bookingQuantity) AS iPadsInUse, ";
			$sql .= "bookingDateTime, returnDateTime ";
			$sql .= "FROM bookings ";
			$sql .= "WHERE DATE(bookingDateTime) = '$bookingDate' ";
			$sql .= "AND TIME(bookingDateTime) BETWEEN '$bookingTime' AND '$returnTime'";
			$sql .= "AND bookingCategory = '$bookingCategory'"; 

			// this checks whether there are any iPads booked out before our booking time and have not yet been returned
			$sql .= "OR DATE(bookingDateTime)  = '$bookingDate' "; 
			$sql .= "AND TIME(bookingDateTime)<= '$bookingTime' ";
			$sql .= "AND TIME(returnDateTime) >= '$returnTime' ";
			$sql .= "AND bookingCategory = '$bookingCategory'"; 

			// this checks if there are any bookings between our booking and return times
			$sql .= "OR DATE(bookingDateTime)  = '$bookingDate' "; 
			$sql .= "AND TIME(returnDateTime) BETWEEN '$bookingTime' AND '$returnTime'" ;
			$sql .= "AND bookingCategory = '$bookingCategory'"; 
			
			$result = mysqli_query($conn, $sql);
						
			if($result)
			{
				$row = mysqli_fetch_assoc($result);
	
				$laptopsInStock = 10;						   // the total number of laptops that are in stock - this can be changed as needed
				$iPadsInStock   = 10; 					       // the total number of iPads that are in stock - this can be changed as needed
				$numOfStaff     = $row['numOfStaff'];	       // the total number of staff that are using iPads at specified date/times
				$iPadsInUse     = $row['iPadsInUse'];          // the total number of iPads at specified date/times
				$iPadsAvbl      = $iPadsInStock - $iPadsInUse; // the number of iPads/Laptops that are available at specified date/times
				
				if ($iPadsInUse == 0 )
				{
					$iPadsInUse = 0; // this is used to display '0' instead of null
				}
				
				// if there are no iPads available at the specified date/times, don't allow the user to book
				
				if ($bookingQuantity > $iPadsAvbl)
				{
					$errMsg  = "There are not enough ".$bookingCategory."s available at that time. <br />";
					$errMsg .= "Number of ".$bookingCategory."s available: <b>".$iPadsAvbl."/".$iPadsInStock."</b><br />";
					$errMsg .= "Booking Quantity: ".$bookingQuantity;
					$errDivDisplay = "block";
				} else {
					
					// insert the booking into database 
					include 'insert_booking.php'; 
				}
			}
		}
	}		
}
?>

<!-- div container that runs along the very top of the page for holding login and date/time info -->
<div class="infobar"> 
<b><?php echo $msg." ID: ".$_SESSION['staffID']; ?></b>. <a href="logout.php">Logout</a>

<!-- div for displaying live date/time -->
<div id="datetime"></div>
</div>

<div class="form-wrapper">
<form method="post">
<p><h2>Make Booking</h2></p>

<div class="form-label">Booking Category: </div>
<div class="controls">
<select name="bookingCategory" class="input-block-level">
	<option value="iPad">iPad</option>
	<option value="Laptop">Laptop</option>
</select>
</div>

<p>
<div class="form-label">Booking Quantity: </div>
<select name="bookingQuantity" class="input-block-level">
	<?php
	for ($i = 1; $i <= 20; $i ++)
	{
		echo "<option value='".$i."'>".$i."</option>";
	}
	?>
</select>

<p>

<?php 
$date = date('Y/m/d');
?>
<div class="form-label">Booking Date: </div>
<input type="text" class="input-block-level" id="datepicker" name="bookingDate" placeholder="Booking Date" value="<?php echo $date; ?>"/>


<div class="form-label">Booking Time: </div>
		<select name="bookingTimeHour" class="input-time-hour">
		
		<?php
		
			for($hours = 8; $hours <= 18; $hours++)
			{
				echo "<option>".str_pad($hours,2,'0',STR_PAD_LEFT)."</option>";						   
			}
		?>
		</select>
		
		<select name="bookingTimeMinute" class="input-time-hour">
		
		<?php
		
			for($minutes = 0; $minutes <= 55; $minutes+=5)
			{
				echo "<option>".str_pad($minutes,2,'0',STR_PAD_LEFT)."</option>";						   
			}
		?>
		</select>
	
	<p>
	
	<div class="form-label">Return Time: </div>
		<select name="returnTimeHour" class="input-time-hour">
		
		<?php
		
			for($hours = 8; $hours <= 18; $hours++)
			{
				echo "<option>".str_pad($hours,2,'0',STR_PAD_LEFT)."</option>";						   
			}
		?>
		</select>
		
		<select name="returnTimeMinute" class="input-time-hour">
		
		<?php
		
			for($minutes = 0; $minutes <= 55; $minutes+=5)
			{
				echo "<option>".str_pad($minutes,2,'0',STR_PAD_LEFT)."</option>";						   
			}
		?>
		</select>
		
	<p>

	<input type="submit" class="btn-booking" name="submitBtn" value="Place Booking" />
</form>
</div>

<?php

// divs for displaying booking confirmation/errors

echo "<div id='form-errors' style='display: $errDivDisplay'>$errMsg</div>";
echo "<div id='form-success' style='display: $successDivDisplay'>$successMsg</div>";

?>

</body>

</html>
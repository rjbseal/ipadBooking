<?php
			//**********************************************
			//*
			//*  INSERT booking into database
			//*
			//**********************************************
			
			$sql_insert  = "INSERT into bookings (staffID, bookingCategory, bookingQuantity, bookingDateTime, returnDateTime) ";
			$sql_insert .= "VALUES ('$staffID', '$bookingCategory', '$bookingQuantity', '$bookingDateTime', '$returnDateTime')";
		
			$result = mysqli_query($conn, $sql_insert);
			
			// if the insert was successfull
			
			if ($result)
			{
				$successDivDisplay = "block";
				$successMsg .= "Booking made for ".$bookingDateTime."<br />";
				$successMsg .= "Returning ".$returnDateTime."<br />";
				$successMsg .= $bookingQuantity." ".$bookingCategory."(s)<br />";
				
			} else {
				
				// something went horribly wrong, display error info
				
				$errDivDisplay = "block";
				$errMsg  = "Failed to insert record: ".$sql_insert."<br>";
				$errMsg .= mysqli_errno($conn).": ".mysqli_error($conn);			
			}
			
?>
<?php
session_start();
?>

<html>
<head>
	<title>Login to Booking App</title>
	<link rel="stylesheet" href="css/login.css">
</head>

<body>

<?php

// File containing database connection

include 'dbConnect.php';

$errMsg = "";
$divDisplay = "none";

// If the form has been submitted, check fields are not empty

if (isset($_POST['submitBtn']))
{
	// if the username and password fields are empty
	
	if (empty($_POST['username']) && empty($_POST['password']))
	{
		$errMsg = "You must enter a username and password.";
		$divDisplay = "block";
	}
	
	// if the password field is empty
	
	elseif (empty($_POST['password']))
	{
		$errMsg = "You must enter a password.";
		$divDisplay = "block";
	}
	
	// if the username field is empty
	
	elseif (empty($_POST['username']))
	{
		$errMsg = "You must enter a username.";
		$divDisplay = "block";
	}
	
	// all fields have been filled in, set variables to user entered values
	
	else {
		
		
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$sql  = "SELECT staffID, staffFirstName, staffLastName, staffUsername, staffPassword ";
		$sql .= "FROM staff ";
		$sql .= "WHERE staffUsername = '$username' AND staffPassword = '$password'";
		
		$result  = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($result);
		$numRows = mysqli_num_rows($result);
		
		$staffID = $row['staffID'];
		
		// if the number of rows returned is = 1, then we have a valid user
		
		if ($numRows == 1)
		{
			// Create a session for this logged in user and redirect to booking page
			
			$_SESSION['staffID']  = $staffID;
			$_SESSION['username'] = $username;

			header('location: make_booking.php');
		} else {
			
			// we have an invalid user
			
			$errMsg = "Login details not recognised.";
			$divDisplay = "block";
		}
	}
}


?>

<!-- Data entry form to capture user login details -->

<div class="form-signin">
<form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
<h2>Please sign in</h2>
<p>
	<input type="text" class="input-block-level" name="username" placeholder="Username">
</p>
<p>
	<input type="password" class="input-block-level" name="password" placeholder="Password">
</p>
<p>
	<input type="submit" class="btn-large btn-primary btn-block" name="submitBtn" value="Submit">
</p>
</form>
</div>
<?php

// form errors div

echo "<div id='form-errors' style='display: $divDisplay'>$errMsg</div>";

?>

</body>
</html>

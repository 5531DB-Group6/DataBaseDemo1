<?php

// form data
$email = strip_tags($_POST['email']);
$password = md5(strip_tags($_POST['password']));
$repeatpassword = md5(strip_tags($_POST['repeatpassword']));
$DOB = strip_tags($_POST['dateOfBirth']);
$status = strip_tags($_POST['status']);
$interest = strip_tags($_POST['interest']);
$profession = strip_tags($_POST['profession']);
$region = strip_tags($_POST['region']);

$submit = $_POST['submit'];

$date = date ("y-m-d");

	if($submit)
	{
		//check for existance 
		if($email&&$password&&$repeatpassword){
			
			// check password match 
			if($password == $repeatpassword){
				
				// check string length 
				if(strlen($email)>45  || strlen($status)>45 || strlen($interest)>45
						|| strlen($region)>45 || strlen($profession)>45)
				{
					echo "Max limit for email, status, interest, region, profession are 45 characters! <a href='registerPage.php'>Click to return </a>";
				}
				else
				{
					// check password length
					if (strlen($password)>25 || strlen($password) <6)
					{
						echo "password must be between 6 and 25 characters ! <a href='registerPage.php'>Click to return </a>";
					}
					else
					{
					// register user
						echo "success!";
					}
				}
				
			}
			else 
				echo "Your password does not match! <a href='registerPage.php'>Click to return </a>";

		}
		else
			echo "please fill in all <b>must </b> fields! <br> <a href='registerPage.php'>Click to return </a>";
	}

?>
<?php 
	echo "<h1>Register</h1>";
	
	
	// form data
	$email = strtolower((strip_tags($_POST['email'])));
	$password = (strip_tags($_POST['password']));
	$repeatpassword = (strip_tags($_POST['repeatpassword']));
	$DOB = strip_tags($_POST['dateOfBirth']);
	$status = strip_tags($_POST['status']);
	$interest = strip_tags($_POST['interest']);
	$profession = strip_tags($_POST['profession']);
	$region = strip_tags($_POST['region']);
	
	$submit = $_POST['submit'];

	if($submit)
	{	

		$connect = mysql_connect("localhost","root","")
		or die("Could not connect to MySQL as root");
		
		
		mysql_select_db("POWON",$connect)
		or die("could not select the test_database");
		
		$namecheck = mysql_query("SELECT EMAIL FROM MEMBER WHERE EMAIL='$email' ")
			or die (mysql_error( ));
		
		$count = mysql_num_rows($namecheck);
		
	if ($count == 0 )
	{
		//check for existance
		if($email&&$password&&$repeatpassword){
			
			// check email format 
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				echo  "<b>Invalid email format </b>";
			}
			else{
				

				// check password match
				if($password == $repeatpassword){
				
					// check string length
					if(strlen($email)>45  || strlen($status)>45 || strlen($interest)>45
							|| strlen($region)>45 || strlen($profession)>45)
					{
						echo "<b> Max limit for email, status, interest, region, profession are 45 characters! </b>";
					}
					else
					{
						// check password length
						if (strlen($password)>25 || strlen($password) <6)
						{
							echo "<b>password must be between 6 and 25 characters ! </b>";
						}
						else
						{
							// encripted password
							//$password = md5($password);
							//$repeatpassword = md5($repeatpassword);
				
							
							// register user
							// DOB cannot be ''
							if (strlen($DOB)==0)
							{
								$query = mysql_query("
										INSERT INTO MEMBER VALUES (NULL, '$password', '$email', NULL, CURRENT_TIMESTAMP, '$status',
										'$interest', '$profession', '$region');
										")
										or die (mysql_error( ));
							}
							else{
								$query = mysql_query("
										INSERT INTO MEMBER VALUES (NULL, '$password', '$email', '$DOB', CURRENT_TIMESTAMP, '$status',
										'$interest', '$profession', '$region');
										")
										or die (mysql_error( ));
							}
				
							die ("<b> you have been registered! <a href='loginPage.php'> Click </a> return to login page </b>");
						}
					}
				
				}
				else
					echo "<b> Your password does not match! </b>";
				
			}

		}
		else
			echo "please fill in all <b>must </b> fields! <br> ";
	}
	else 
		echo "<b>email already registered </b>";
	}
	
?>

<html>
	<form action ='registerPage.php' method='POST'>
		<table>
			<tr>
				<td>
				Email address (must):
				</td>
				<td>
				<input type =  'email' name= 'email' placeholder="john@example.net" value='<?php echo $email;?>'>
				</td>
			</tr>
			
			<tr>
				<td>
				Your password (must):
				</td>
				<td>
				<input type =  'password' name= 'password'>
				</td>
			</tr>
			
			<tr>
				<td>
				Repeat password (must):
				</td>
				<td>
				<input type =  'password' name= 'repeatpassword'>
				</td>
			</tr>
			
			<tr>
				<td>
				Date of birth:
				</td>
				<td>
				<input type = 'date' name= 'dateOfBirth' placeholder="1970-12-31"value='<?php echo $date;?>'>
				</td>
			</tr>
			
			<tr>
				<td>
				Your status:
				</td>
				<td>
				<input type =  'text' name= 'status' placeholder="work" value='<?php echo $status;?>'>
				</td>
			</tr>
			
			<tr>
				<td>
				Interest:
				</td>
				<td>
				<input type =  'text' name= 'interest' placeholder="Programming" value='<?php echo $interest;?>'>
				</td>
			</tr>
			
			<tr>
				<td>
				Profession:
				</td>
				<td>
				<input type =  'text' name= 'profession' placeholder="Developer" value='<?php echo $profession;?>'>
				</td>
			</tr>
			
			<tr>
				<td>
				Region:
				</td>
				<td>
				<input type =  'text' name= 'region' placeholder="Montreal" value='<?php echo $region;?>'>
				</td>
			</tr>
			
		</table>

		<input type =  'submit' name='submit' value= 'submit'><br>
		
	</form>

</html>
<?php

session_start();
	
	$email = $_POST['email'];
	$password = $_POST['password'];

	
	if ($email&&$password){
		$connect = mysql_connect("localhost","root","") or die("Couldn't connect to mysql as root");
		mysql_select_db("POWON",$connect) or die("Could not find db");
		
		$query = mysql_query("SELECT * FROM  MEMBER WHERE EMAIL='$email'");
		
		$numrows= mysql_num_rows($query);
				
		if($numrows != 0){
			// execute the code 
			while ($row = mysql_fetch_assoc($query))
			{
				$dbemail = $row['EMAIL'];
				$dbpassword = $row['PASSWORD'];
				
			}
			if ($email == $dbemail && ($password) == $dbpassword)
			{
				echo "you are in ! <a href='member.php'> Click </a> here to enter the member page. ";
				$_SESSION['EMAIL'] = $dbemail;
			}
			else 
				echo "Incorrect password";
		}
		else 
			die ("User doesn't exit.");
		
	}
	else{
		die("please enter email and password");
	}

?>
<?php
	ini_set('mysql.connect_timeout', 300);
	ini_set('default_socket_timeout',300);
			
?>

<html>
	<head>
		<meta charset="UTF-8">
		<title>Uploading Images</title>
	</head>
	
	<body>
		<em><font color="#ff8000"> 
			Reminder: there should have a table named uploadimages <br>
			<br><br><br>
		</font></em>
	<body>
	
	
	<body>
		<form method = "post" enctype ="multipart/form-data">
		<b><font color="#8000ff"> Select Image to Upload: </font></b><br>
		<br/>
			<input type = "file" name="image" />
			<br/><br/>
			<input type="submit" name="sumit" value = "Upload Now" />
		</form>
	
<?php 
			if (isset($_POST['sumit'])){
				if (getimagesize($_FILES['image']['tmp_name'])== FALSE){
					echo "are you kinding me? Please select an image.";
				}else{
					  $image=addslashes($_FILES['image']['tmp_name']);
					  $name=addslashes($_FILES['image']['name']);
					  $image= file_get_contents($image);
					  $image=base64_encode($image);
					  saveimage($name, $image);
				}
				displayimage();
			}
			    
			
			function saveimage($name, $image){
				$con=mysqli_connect("localhost:3306", "root","");
				mysqli_select_db($con,"5531project");
				$qry= "insert into uploadimages (name, image) values ('$name', '$image')";
				
				$result = mysqli_query($con, $qry);
				
				if ($result){
					echo "<br/> Image uploaded.<br>";
				}else{
					echo "<br/> image failed to upload";
				}
			}
			
			function displayimage(){
				$con=mysqli_connect("localhost:3306", "root","");
				mysqli_select_db($con,"5531project");
				$qry="select * from uploadimages";
				$result = mysqli_query($con,$qry);
				while ($row=mysqli_fetch_array($result)){
					echo '<img height="150" width="150" src = "data:uploadimages;base64,'.$row[2].'">';
				}
				mysqli_close($con);
			}
				
?>
</body>
		<br><br><br><br><br><br>
		<TABLE width=500 height=50 bgColor=#FFFAFA border=8 bordercolor="#8000ff "><TR><TD><MARQUEE><font style=font:40npt =PmingLiu color=red>Welcome to<BR>DataBase Group MiniFacebook Project<BR><BR>Just join in£¡</TD></TR></TBODY></TABLE> </MARQUEE><EMBED src=http://mp3goo.com/download/taylor-swift-styles/  
			width=0 height=0 type=audio/x-ms-wma autostart="true" loop="true">
	</body>
	
</html>


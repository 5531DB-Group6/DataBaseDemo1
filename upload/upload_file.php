<?php
/*
	$file_result = "";
	if ($_FILES ["file"] ["error"]>0){
		$file_result .= "No File Uploaded or Invalide File";
		$file_result .= "Error Code: " .$_FILES["file"]["error"]."<br>";
	}else{
		$file_result .=
		"Upload: ".$_FILES["file"]["name"] ."<br>".
		"Type: " .$_FILES["file"]["type"] ."<br>".
		"Size: " . ($_FILES["file"]["size"]/1024) ."<br>".
		"Temp file:: " .$_FILES["file"]["tmp_name"] ."<br>";
		
		move_uploaded_file($_FILES["file"]["tmp_name"],
				"D:/files_server/" .$_FILES["file"]["name"]);
		$file_result .= "File Upload Succeseful!";
	}
*/
$destination_folder="D:/files_sever/";
$max_file_size=2000000; //20M
$destination = $destination_folder.time();

if (isset($_POST ['upload'])){
	$file_name = $_FILES['file']['name'];
	$file_type = $_FILES['file']['type'];
	$file_size = $_FILES['file']['size'];
	$file_tmp_name = $_FILES['file']['tmp_name'];

	if($max_file_size < $file_size)
	{
		echo "file is too big";
		exit;
	}
	
	if ($file_name ==''){
		echo "<script>alert('Please select an file to upload')</script>";
		exit();
	}else{
		move_uploaded_file ($file_tmp_name, "D:/files_server/$file_name");
		echo "file Uploaded Successfully";			
	}
}
?>
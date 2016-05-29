

<?php
$supporttypes=array(
		'image/jpg',
		'image/jpeg',
		'image/png',
		'image/pjpeg',
		'image/gif',
		'image/bmp',
		'image/x-png'
);
$destination_folder="D:/images/";
$imgpreview=1;
$max_image_size=2000000;
$destination = $destination_folder.time();

	if (isset($_POST ['upload'])){
		$image_name = $_FILES['image']['name'];
		$image_type = $_FILES['image']['type'];
		$image_size = $_FILES['image']['size'];
		$image_tmp_name = $_FILES['image']['tmp_name'];
		
		if($max_image_size < $image_size)
		{
			echo "Image is too big";
			exit;
		}
		
		
		/*
		if(!in_array($image["type"], $supporttypes))
		{
			echo "Sorry, not support this type of image".$image["type"];
			exit;
		}
		
		$filename=$file["tmp_name"];
		$image_size = getimagesize($filename);
		$pinfo=pathinfo($image["name"]);
		$ftype=$pinfo['extension'];
		$destination = $destination_folder.time().".".$ftype;
		if (file_exists($destination) && $overwrite != true)
		{
			echo "it already the same name";
			exit;
		}
		*/
		
		if ($image_name ==''){
			echo "<script>alert('Are you kiding me?? Please select an image to upload')</script>";	
			exit();
		}else{
			move_uploaded_file ($image_tmp_name, "D:/images/$image_name");
			echo "Image Uploaded Successfully  Here is the image";
			echo "<img src = 'D:/images/$image_name.time()'>";
			
		}
	}
?>

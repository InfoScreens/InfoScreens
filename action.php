<?php

//echo sha1(sha1("5555")."siloponni");

$fileDir = "files";

if(isset($_GET['uploadfiles'])){
	$data = array();
	$error = false;
	$files = array();

	if(!is_dir($fileDir)){
		mkdir($fileDir, 0777);
	}

	
	foreach($_FILES as $file){
		
		if(move_uploaded_file($file['tmp_name'], $fileDir."/".basename($file['name']))){
			$files[] = basename($file['name']);
		}else{
			$error = true;
		}	

	}
	$data = $error ? array('error' => 'Error with file uploading') : array('files' => $files);
	echo json_encode($data);//*/

}








?>
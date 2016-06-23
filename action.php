<?php

//echo sha1(sha1("5555")."siloponni");

$fileDir = "files/";

if(isset($_GET['uploadfiles'])){
	$data = array();
	$error = false;
	$files = array();
	if(!is_dir($fileDir)){
		mkdir($fileDir, 0777);
	}

	var_dump($_FILES);
	foreach($_FILES as $file){
		//echo $file['tmp_name']."  ".$fileDir.basename($file['name']);
		//echo move_uploaded_file($file['tmp_name'], $fileDir.basename($file['name']));
		//var_dump($_SERVER);
		
		if(move_uploaded_file($file['tmp_name'], $fileDir.basename($file['name']))){
			//$files[] = realpath($fileDir.$file['name']);
			$files[] = $fileDir.basename($file['name']);
		}else{
			$error = true;
		}	//*/

	}
	$data = $error ? array('error' => 'Error with file uploading') : array('files' => $files);
	echo json_encode($data);//*/

}








?>
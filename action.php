<?php

//echo sha1(sha1("5555")."siloponni");

/*
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);//*/

$fileDir = "files";

function getThumbnail($fileDir, $srcFile){
	shell_exec("convert ".$fileDir."/".$srcFile."[0] ".$fileDir."/".$srcFile.".jpg");
	if(!is_dir($fileDir."/thumnails")){
		mkdir($fileDir."/thumbnails", 0777);
	}
	if(copy($fileDir."/".$srcFile.".jpg", $fileDir."/thumbnails/".$srcFile.".jpg")){
		unlink($fileDir."/".$srcFile.".jpg");
	}
}

if(isset($_GET['uploadfiles'])){
	$data = array();
	$error = false;
	$files = array();
	$response = array();

	if(!is_dir($fileDir)){
		mkdir($fileDir, 0777);
	}
	
	foreach($_FILES as $file){
		$filename = basename($file['name']);
		
		if(move_uploaded_file($file['tmp_name'], $fileDir."/".$filename)){
			$files[] = $filename;
			getThumbnail($fileDir, $filename);
			include "db_connect.php";
			$sql_query = "INSERT INTO `files`(`fileName`, `uploadTime`, `type`) VALUES ('".$filename."', NOW(), '".$file['type']."')";
			mysql_query($sql_query);
			$sql_query = "SELECT * FROM `files` WHERE `fileName` = '".$filename."'";
			$result = mysql_query($sql_query);
			while($fileinfo = mysql_fetch_assoc($result)){
				$data = $fileinfo;
			}



			mysql_close($db);
		}else{
			$error = true;
		}	
	}

	
	if($error == true){
		$response = array('error' => 'File uploading error');
	}else{
		$response = $data;
	}//*/
	
	echo json_encode($response);//*/

}//*/









?>
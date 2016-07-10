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

include_once ("users.php");
include_once ("auth.php");

/**
 * Extract action parameters from POST request parameters
 *
 * @param	$keys_and_default_values	associative array of requested keys (as keys) associated default values (as values)
 * @return								associative array with requested keys (as keys) and associated values or default values
 */
function get_action_parameters ($keys_and_default_values) {

	global $_POST;

	// parse json from POST request parameters
	try {
		$parameters = json_decode ($_POST["params"]);
	} catch (Exception $exception) {
		$parameters = array ();
	}

	$result = array ();
	// set values for keys found in POST request parameters
	foreach ($parameters as $key => $value) {
		if (array_key_exists ($key, $keys_and_default_values)) {
			$result[$key] = $value;
		}
	}
	// set default values for keys not found in POST request parameters
	foreach ($keys_and_default_values as $key => $value) {
		if (!array_key_exists ($key, $result)) {
			$result[$key] = $value;
		}
	}
	return $result;
}

if (isset ($_POST["action"])) {

	$action = $_POST["action"];

	// handle requested action
	switch ($action) {
		case "create_user":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"email" => "",
					"password" => "",
					"name" => "",
					"surname" => "",
					"is_admin" => 0
				)
			);
			// create user
			$response = $users->create (
				$parameters["email"],
				$parameters["password"],
				$parameters["name"],
				$parameters["surname"],
				$parameters["is_admin"]
			);
			break;
		case "get_currrent_user_info":
			// if auhtorized
			$result = $auth->get_authorized_id ();
			if (!$result->errored ()) {
				// get user info
				$result = $users->get_info ($result->data);
			}
			$response = $result;
			break;
		case "get_users_list":
			// get user list
			$response = $users->get_list ();
			break;
		case "set_user_is_admin":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"user_id" => "",
					"is_admin" => 0
				)
			);
			// set user permissions
			$response = $users->set_info (
				$parameters["user_id"],
				"is_admin",
				$parameters["is_admin"]
			);
			break;
		default:
			$response = new Response (Errors::UNKNOWN_ACTION);
	}

	// send back result in json
	header ("Content-Type: application/json");
	echo $response->to_json ();
}







?>
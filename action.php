<?php

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

	$file = $_FILES["file"];
	$hash = hash_file("sha256", $file["tmp_name"]);
	$sql_query = "SELECT COUNT(*) FROM `files` WHERE `hash` = '".$hash."'";
	include "db_connect.php";
	$query = mysql_query($sql_query);
	while($item = mysql_fetch_array($query)){
		if($item[0] == 0){
			$isExists = false;
		}else{
			$isExists = true;
		}
	}
	
	if(!$isExists){ //если файла нет в базе - добавляем, если есть - получаем инфу о нём
		$filename = basename($file['name']);
		if(move_uploaded_file($file['tmp_name'], $fileDir."/".$filename)){
			$hash = hash_file("sha256", $fileDir."/".$filename);
			getThumbnail($fileDir, $filename);
			$sql_query = "INSERT INTO `files`(`fileName`, `uploadTime`, `type`, `hash`) VALUES ('".$filename."', NOW(), '".$file['type']."', '".$hash."')";
			mysql_query($sql_query);
			$sql_query = "SELECT * FROM `files` WHERE `fileName` = '".$filename."'";
			$result = mysql_query($sql_query);
			while($fileinfo = mysql_fetch_assoc($result)){
				$data = $fileinfo;
			}
		}else{
			$error = true;
		}
	}else{
		
		$sql_query = "SELECT * FROM `files` WHERE `hash` = '".$hash."'";
		$response = mysql_query($sql_query);
		while($fileinfo = mysql_fetch_assoc($response)){
			//var_dump($fileinfo);
			$data = $fileinfo;
		}

	}

	
	if($error == true){
		$response = array('error' => 'File uploading error');
	}else{
		$response = $data;
	}

	mysql_close($db);
	echo json_encode($response);

}

include_once ("users.php");
include_once ("auth.php");
include_once ("devices.php");
include_once ("groups.php");

/* TODO: make more strict, remove default values and return errored Response */
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
					"is_admin" => 0,
					"group_id" => ""
				)
			);
			// create user
			$response = $users->create (
				$parameters["email"],
				$parameters["password"],
				$parameters["name"],
				$parameters["surname"],
				$parameters["is_admin"],
				$parameters["group_id"]
			);
			break;
		/* TODO: remove typo in word "current" ("currRent") */
		case "get_currrent_user_info":
			// if auhtorized
			$result = $auth->get_authorized_id ();
			if (!$result->errored ()) {
				// get user info
				$result = $users->get ($result->data);
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
		case "create_device":
			// create device
			$response = $devices->create ();
			break;
		case "get_devices_list":
			// get devices list
			$response = $devices->get_list ();
			break;
		case "allow_device_to_user":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"device_id" => "",
					"user_id" => "",
					"allow" => 0
				)
			);
			// allow device to user
			$response = $devices->allow_to_user (
				$parameters["device_id"],
				$parameters["user_id"],
				intval ($parameters["allow"])
			);
			break;
		case "get_list_of_user_devices":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"user_id" => "",
				)
			);
			// get list of user devices
			$response = $devices->get_list_of_user (
				$parameters["user_id"]
			);
			break;
		case "set_device_name_for_user":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"device_id" => "",
					"user_id" => "",
					"name" => ""
				)
			);
			// set user device name
			$response = $devices->set_name_for_user (
				$parameters["device_id"],
				$parameters["user_id"],
				$parameters["name"]
			);
			break;
		case "get_groups_list":
			// get group list
			$response = $groups->get_list ();
			break;
		case "create_group":
			// get group list
			$response = $groups->create ();
			break;
		case "set_group_name":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"group_id" => "",
					"name" => ""
				)
			);
			// set group name
			$response = $groups->set_name (
				$parameters["group_id"],
				$parameters["name"]
			);
			break;
		case "get_group":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"group_id" => ""
				)
			);
			// set group name
			$response = $groups->get (
				$parameters["group_id"]
			);
			break;
		case "get_list_of_group_users":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"group_id" => "",
				)
			);
			// get list of group users
			$response = $users->get_list_of_group (
				$parameters["group_id"]
			);
			break;
		case "allow_device_to_group":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"device_id" => "",
					"group_id" => "",
					"allow" => 0
				)
			);
			// allow device to group
			$response = $devices->allow_to_group (
				$parameters["device_id"],
				$parameters["group_id"],
				intval ($parameters["allow"])
			);
			break;
		case "get_list_of_group_devices":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"group_id" => "",
				)
			);
			// get list of group devices
			$response = $devices->get_list_of_group (
				$parameters["group_id"]
			);
			break;
		case "get_user":
			// extract action parameters
			$parameters = get_action_parameters (
				array (
					"user_id" => ""
				)
			);
			// set group name
			$response = $users->get (
				$parameters["user_id"]
			);
			break;
		default:
			$response = new Response (Errors::UNKNOWN_ACTION);
	}

	// send back result in json
	header ("Content-Type: application/json");
	echo $response->to_json ();
}

if(isset($_GET['saveItem'])){
  	$data = json_decode($_POST["data"], true);
	include "db_connect.php";
	$sql_query = "INSERT INTO `schedule`(`device`, `date`, `fileId`, `startTime`, `endTime`) VALUES ('".$data['mon']."', '".$data['date']."', '".$data['id']."',  '".$data['start']."', '".$data['end']."')";
	$query = mysql_query($sql_query);
	echo mysql_error();

	//echo "sql_query: ".$sql_query;
	

}//*/

if(isset($_GET['removeItem'])){
	//$data = json_decode($_POST["data"], true);
	$fileId = $_POST["fileId"];
	include "db_connect.php";
	$sql_query = "DELETE  FROM `schedule` WHERE fileId = ".$fileId;
	//echo $sql_query;
	$query = mysql_query($sql_query);
	echo mysql_error();
	mysql_close($db);
}


if(isset($_GET["openSchedule"])){
	$data = json_decode($_POST["data"], true);
	include "db_connect.php";
	$sql_query = "SELECT * FROM `schedule` WHERE device = '".$_POST["mon"]."' AND date = '".$_POST["date"]."'";
	$query = mysql_query($sql_query);
	$response = array();
	while($item = mysql_fetch_assoc($query)){
		$response[] = $item;
	}
	echo json_encode($response);
	mysql_close($db);
}






?>
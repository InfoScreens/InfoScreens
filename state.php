<?php

// util functions
function get_param ($name, $default = "", $from = null) {
	$from = $from == null ? $_GET : $from;
	return isset ($from[$name]) ? $from[$name] : $default;
}
function get_json_data ($required_fields) {
	try {
		$data = json_decode (get_param ("data"), true);
	} catch (Exception $e) {
		return null;
	}
	foreach ($required_fields as $i) {
		if (!array_key_exists ($i, $data)) {
			return null;
		}
	}
	return $data;
}

// logic functions
function set_state ($device_id, $app_id, $state) {
	global $states;
	if (!array_key_exists ($device_id, $states)) {
		$states[$device_id] = array ();
	}
	$states[$device_id][$app_id] = array (
		"state" => $state,
		"last_changed_time" => time ()
	);
	return null;
}
function get_states ($device_id) {
	global $states;
	if (!array_key_exists ($device_id, $states)) {
		$states[$device_id] = array ();
	}
	return $states[$device_id];
}

// load states
$states = array ();
$state_file_name = "state.json";
if (is_file ($state_file_name)) {
	$states = json_decode (file_get_contents ($state_file_name), true);
}

// work
$method = get_param ("method");
switch ($method) {
	case "set_state":
		$data = get_json_data (array ("device_id", "app_id", "state"));
		if ($data != null) {
			$result = set_state ($data["device_id"], $data["app_id"], $data["state"]);
		} else {
			$result = "not enough parameters";
		}
		break;
	case "get_states":
		$device_id = get_param ("device_id");
		$result = get_states ($device_id);
		break;
	default:
		$result = "unknown method";
}
echo json_encode ($result);

// save states
file_put_contents ($state_file_name, json_encode ($states));

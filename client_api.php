<?php

include_once ("x.php");
include_once ("utils.php");

function get_schedule ($device_id) {

	include_once ("db_connect.php");

	global $utils;

	$escaped_device_id = $utils->escape_sql ($device_id);
	$escaped_date = $utils->escape_sql (date ("Y-m-d"));

	$result = mysql_query (
		sprintf (
			"SELECT * FROM `schedule` NATURAL JOIN `files` WHERE `device` = '%s' AND `date` = '%s';",
			$escaped_device_id,
			$escaped_date
		)
	);
	if (!$result) {
		return new Response (null, Errors::DB_QUERY_FAILED);
	}

	$items = array ();
	while ($row = mysql_fetch_array ($result)) {
		$items[] = array (
			"item_id" => $row["itemId"],
			"type" => $row["type"],
			"start_time" => strtotime ($row["startTime"]),
			"end_time" => strtotime ($row["endTime"]),
			"params" => array (),
			"content_links" => array (
				$row["fileName"]
			),
			"position" => array (
				0, 0,
				100, 100
			)
		);
	}
	return new Response ($items);
}

function get_param ($name, $default = "", $from = null) {
	$from = $from == null ? $_GET : $from;
	return isset ($from[$name]) ? $from[$name] : $default;
}

$method = get_param ("method");

switch ($method) {
	case "get_schedule":
		$device_id = get_param ("device_id");
		$result = get_schedule ($device_id);
		break;
	default:
		$result = new Response (null, Errors::UNKNOWN_ACTION);
}

echo json_encode ($result);

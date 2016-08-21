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

	$first_time = 0xFFFFFFFF;
	$last_time = 0;

	$items = array ();
	while ($row = mysql_fetch_array ($result)) {
		$first_time = min ($first_time, intval ($row["startTime"]));
		$last_time = max ($last_time, intval ($row["endTime"]));
		switch ($row["type"]) {
			case "user/text":
				$type = 2;
				break;
			case "user/line":
				$type = 3;
				break;
			case "image/jpeg":
			case "image/png":
			case "image/bmp":
			case "image/gif":
				$type = 4;
				break;
			case "video/avi":
			case "video/mp4":
			case "video/webm":
			case "video/ogg":
			case "video/quicktime":
				$type = 5;
				break;
			case "user/background":
				$type = 0;
				break;
			case "application/pdf":
				$type = 6;
				break;
			case "user/timer":
				$type = 7;
				break;
			case "user/stopwatch":
				$type = 8;
				break;
			case "user/score":
				$type = 9;
				break;
			case "user/search":
				$type = 10;
				break;
			default:
				$type = 1;
		}
		if ($row["element_type"] != 1) {
			$type = $row["element_type"];
		}
		$items[] = array (
			"item_id" => $row["itemId"],
			"type" => $type,
			"start_time" => intval ($row["startTime"]),
			"end_time" => intval ($row["endTime"]),
			"params" => array (),
			"content" => array (
				$row["fileName"]
			),
			"position" => array (
				"x1" => $row["x1"], "y1" => $row["y1"],
				"x2" => $row["x2"], "y2" => $row["y2"]
			)
		);
	}
	return new Response (array (
		"start_time" => $first_time,
		"end_time" => $last_time,
		"data" => $items
	));
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

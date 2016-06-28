<?php

include_once ("utils.php");

class Users {
	public function get_info ($id) {

		include_once ("db_connect.php");

		global $utils;

		$escaped_id = $utils->escape_sql ($id);

		$row = mysql_fetch_array (mysql_query ("SELECT * FROM `users` WHERE `userId`='".$escaped_id."';"));
		$info = NULL;

		if ($row != NULL) {
			$info = array (
				"name" => $row["name"],
				"surname" => $row["surname"],
				"email" => $row["email"]
			);
		}

		return $info;
	}
}

$users = new Users ();
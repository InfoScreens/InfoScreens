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
			$info = $this->extract_user_info ($row);
		}

		return $info;
	}

	public function create ($email, $password, $name, $surname, $is_admin) {

		include_once ("db_connect.php");

		include_once ("auth.php");

		global $utils, $auth;

		$escaped_email = $utils->escape_sql ($email);
		$escaped_name = $utils->escape_sql ($name);
		$escaped_surname = $utils->escape_sql ($surname);

		$permissions = $is_admin ? 1 : 0;

		$result = mysql_query (
			sprintf (
				"INSERT INTO `users` (`email`, `name`, `surname`, `permissions`) VALUES ('%s', '%s', '%s', %d);",
				$escaped_email, $escaped_name, $escaped_surname, $permissions
			)
		);

		if ($result) {

			$user_id = mysql_insert_id ();

			$auth->set_user_credentials ($user_id, $email, $password);
		}
		return $result;
	}

	public function set_info ($id, $key, $value) {

		include_once ("db_connect.php");

		global $utils;

		$result = false;

		$escaped_id = $utils->escape_sql ($id);

		if ($key == "is_admin") {

			$value = (is_numeric ($value) ? intval ($value) : 0) ? 1 : 0;

			$result = mysql_query (
				sprintf (
					"UPDATE `users` SET `permissions` = %d WHERE `userId` = '%s';",
					$value,
					$escaped_id
				)
			);
		}

		return $result;
	}

	public function get_list () {

		include_once ("db_connect.php");

		$result = mysql_query (
			sprintf (
				"SELECT `userId`, `email`, `name`, `surname`, `permissions` FROM `users`;"
			)
		);

		$list = array ();
		while ($row = mysql_fetch_array ($result)) {
			$list[] = $this->extract_user_info ($row);
		}

		return $list;
	}

	private function extract_user_info ($row) {
		return array (
			"name" => $row["name"],
			"surname" => $row["surname"],
			"email" => $row["email"],
			"is_admin" => $row["permissions"] == "1",
			"id" => $row["userId"]
		);
	}
}

$users = new Users ();
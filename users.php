<?php

include_once ("utils.php");
include_once ("x.php");
include_once ("auth.php");

class Users {
	public function get ($id) {

		include_once ("db_connect.php");

		global $utils;

		$escaped_id = $utils->escape_sql ($id);

		$row = mysql_fetch_array (mysql_query ("SELECT * FROM `users` WHERE `userId`='".$escaped_id."';"));

		if (!$row) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		return new Response ($this->extract_user_info ($row));
	}

	public function create ($email, $password, $name, $surname, $is_admin) {

		include_once ("db_connect.php");

		global $utils, $auth;

		$result = $utils->check_email ($email);
		if ($result->errored ()) {
			return $result;
		}

		$result = $auth->check_password ($password);
		if ($result->errored ()) {
			return $result;
		}

		$result = $this->check_name ($name);
		if ($result->errored ()) {
			return $result;
		}

		$result = $this->check_surname ($surname);
		if ($result->errored ()) {
			return $result;
		}

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
		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$user_id = mysql_insert_id ();

		$result = $auth->set_user_credentials ($user_id, $email, $password);
		if ($result->errored ()) {
			return $result;
		}

		return new Response ($user_id);
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

			if (!$result) {
				return new Response (null, Errors::DB_QUERY_FAILED);
			}
		}

		return new Response (null);
	}

	public function get_list () {

		include_once ("db_connect.php");

		$result = mysql_query (
			sprintf (
				"SELECT `userId`, `email`, `name`, `surname`, `permissions` FROM `users`;"
			)
		);

		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$list = array ();
		while ($row = mysql_fetch_array ($result)) {
			$list[] = $this->extract_user_info ($row);
		}

		return new Response ($list);
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

	public function check_name ($name) {

		if (strlen ($name) <= 0) {
			return new Response (null, Errors::NAME_IS_EMPTY);
		}

		return new Response (null);
	}

	public function check_surname ($surname) {

		if (strlen ($surname) <= 0) {
			return new Response (null, Errors::SURNAME_IS_EMPTY);
		}

		return new Response (null);
	}
}

$users = new Users ();
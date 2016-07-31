<?php

include_once ("utils.php");
include_once ("x.php");
include_once ("auth.php");

class Users {
	const PERMISSION_ADMIN = 1;
	const PERMISSION_SUPER_ADMIN = 2;

	public function get ($id) {

		include_once ("db_connect.php");

		global $utils, $auth;

		$result = $utils->check_is_user ();
		if ($result->errored ()) {
			return $result;
		}

		if ($auth->get_authorized_id ()->data != $id) {
			$result = $utils->check_is_admin ();
			if ($result->errored ()) {
				return $result;
			}
		}

		$escaped_id = $utils->escape_sql ($id);

		$row = mysql_fetch_array (mysql_query ("SELECT * FROM `users` WHERE `userId`='".$escaped_id."';"));

		if (!$row) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$user = $this->extract_user_info ($row);

		if ($auth->get_authorized_id ()->data != $id && $user["group_id"] != $utils->get_user ()->data["group_id"]) {
			$result = $utils->check_is_super_admin ();
			if ($result->errored ()) {
				return $result;
			}
		}

		return new Response ($user);
	}

	public function create ($email, $password, $name, $surname, $is_admin, $group_id) {

		include_once ("db_connect.php");

		global $utils, $auth;

		$result = $utils->check_is_admin ();
		if ($result->errored ()) {
			return $result;
		}

		if ($is_admin || $utils->get_user ()->data["group_id"] != $group_id) {
			$result = $utils->check_is_super_admin ();
			if ($result->errored ()) {
				return $result;
			}
		}

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
		$escaped_group_id = $utils->escape_sql ($group_id);

		$permissions = $is_admin ? 1 : 0;

		$result = mysql_query (
			sprintf (
				"INSERT INTO `users` (`email`, `name`, `surname`, `permissions`, `group_id`) VALUES ('%s', '%s', '%s', %d, '%s');",
				$escaped_email, $escaped_name, $escaped_surname, $permissions, $escaped_group_id
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

		$result = $utils->check_is_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$result = $this->get ($id);
		if ($result->errored ()) {
			return $result;
		}
		$user = $result->data;

		if ($user["group_id"] != $utils->get_user ()->data["group_id"]) {
			$result = $utils->check_is_super_admin ();
			if ($result->errored ()) {
				return $result;
			}
		}

		$escaped_id = $utils->escape_sql ($id);

		if ($key == "is_admin") {

			$value = (is_numeric ($value) ? intval ($value) : 0) ? 1 : 0;

			if ($value) {
				$result = $utils->check_is_super_admin ();
				if ($result->errored ()) {
					return $result;
				}
			}

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

		global $utils;

		$result = $utils->check_is_super_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$result = mysql_query (
			sprintf (
				"SELECT `userId`, `email`, `name`, `surname`, `permissions`, `group_id` FROM `users`;"
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

	/**
	 * get list of group's users
	 *
	 * @return Response	data is array of results from `extract_user_info` applied to all queried database rows
	 */
	public function get_list_of_group ($group_id) {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_admin ();
		if ($result->errored ()) {
			return $result;
		}

		if ($utils->get_user ()->data["group_id"] != $group_id) {
			$result = $utils->check_is_super_admin ();
			if ($result->errored ()) {
				return $result;
			}
		}

		$escaped_group_id = $utils->escape_sql ($group_id);

		$result = mysql_query (
			sprintf (
				"SELECT `userId`, `email`, `name`, `surname`, `permissions`, `group_id` FROM `users` WHERE `group_id` = '%s';",
				$escaped_group_id
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
		$permissions = intval ($row["permissions"]);
		$info = array (
			"name" => $row["name"],
			"surname" => $row["surname"],
			"email" => $row["email"],
			"is_admin" => $permissions & Users::PERMISSION_ADMIN,
			"id" => $row["userId"],
			"group_id" => $row["group_id"]
		);
		if ($info["is_admin"]) {
			$info["is_super_admin"] = $permissions & Users::PERMISSION_SUPER_ADMIN;
		}
		return $info;
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
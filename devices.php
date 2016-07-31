<?php

include_once ("utils.php");
include_once ("auth.php");
include_once ("x.php");

/**
 * Class Devices
 *
 * collection of devices
 */
class Devices {

	/**
	 * get device
	 *
	 * @param $id		device id
	 * @return Response	data is result of `extract_device_info` applied to queried database row
	 */
	public function get_device ($id) {

		include_once ("db_connect.php");

		global $utils;

		$escaped_id = $utils->escape_sql ($id);

		$result = mysql_query (
			sprintf (
				"SELECT * FROM `devices` WHERE `id` = '%s';",
				$escaped_id
			)
		);

		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$row = mysql_fetch_array ($result);
		if (!$row) {
			return new Response (null, Errors::DEVICE_NOT_EXIST);
		}

		return new Response ($this->extract_device_info ($row));
	}

	/**
	 * create new device
	 *
	 * @return Response	created device id
	 */
	public function create () {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_super_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$result = mysql_query ("SELECT UUID() uuid;");
		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}
		$row = mysql_fetch_array ($result);
		$id = $row["uuid"];

		$escaped_id = $utils->escape_sql ($id);

		$result = mysql_query (
			sprintf (
				"INSERT INTO `devices` (`id`) VALUES ('%s');",
				$escaped_id
			)
		);
		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		return new Response ($id);
	}

	/* TODO: move to superclass, when superclass created */
	/**
	 * set property value
	 *
	 * @param $id		id of device
	 * @param $name		name of property to be set
	 * @param $value	value to be set
	 * @return Response
	 */
	private function set_property ($id, $name, $value) {

		include_once ("db_connect.php");

		global $utils;

		$escaped_id = $utils->escape_sql ($id);

		return new Response (null);
	}

	/**
	 * get list of devices
	 *
	 * @return Response	data is array of results from `extract_device_info` applied to all queried database rows
	 */
	public function get_list () {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$result = mysql_query ("SELECT * FROM `devices`;");

		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$list = array ();
		while ($row = mysql_fetch_array ($result)) {
			$list[] = $this->extract_device_info ($row);
		}

		return new Response ($list);
	}

	/* TODO: return object of class `Device` (when class `Device` is done) */
	/**
	 * extract device information from database row
	 *
	 * @param $row		result of `mysql_fetch_array`
	 * @return array	associative array of properties
	 */
	private function extract_device_info ($row) {
		return array (
			"id" => $row["id"]
		);
	}

	/**
	 * get list of user's devices
	 *
	 * @return Response	data is array of results from `extract_device_info` applied to all queried database rows
	 */
	public function get_list_of_user ($user_id) {

		include_once ("db_connect.php");

		global $utils, $auth;

		$result = $utils->check_is_user ();
		if ($result->errored ()) {
			return $result;
		}

		if ($auth->get_authorized_id ()->data != $user_id) {
			$result = $utils->check_is_admin ();
			if ($result->errored ()) {
				return $result;
			}
		}

		$escaped_user_id = $utils->escape_sql ($user_id);

		$result = mysql_query (
			sprintf (
				"SELECT * FROM `devices` NATURAL JOIN (SELECT `user_id`, `device_id` `id`, `device_name` FROM `user_devices` WHERE `user_devices`.`user_id` = '%s') as a;",
				$escaped_user_id
			)
		);

		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$list = array ();
		while ($row = mysql_fetch_array ($result)) {
			$list[] = $this->extract_user_device_info ($row);
		}

		return new Response ($list);
	}

	/**
	 * get list of group's devices
	 *
	 * @return Response	data is array of results from `extract_device_info` applied to all queried database rows
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
				"SELECT * FROM `devices` NATURAL JOIN (SELECT `group_id`, `device_id` `id` FROM `group_devices` WHERE `group_devices`.`group_id` = '%s') as a;",
				$escaped_group_id
			)
		);

		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$list = array ();
		while ($row = mysql_fetch_array ($result)) {
			$list[] = $this->extract_device_info ($row);
		}

		return new Response ($list);
	}

	/**
	 * allow usage of device by user
	 *
	 * @param $device_id
	 * @param $user_id
	 * @param $allow
	 * @return Response
	 */
	public function allow_to_user ($device_id, $user_id, $allow) {

		include_once ("db_connect.php");

		global $utils, $users;

		$result = $utils->check_is_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$result = $users->get ($user_id);
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

		$escaped_user_id = $utils->escape_sql ($user_id);
		$escaped_device_id = $utils->escape_sql ($device_id);

		if ($allow) {
			$result = mysql_query (
				sprintf (
					"INSERT INTO `user_devices` (`user_id`, `device_id`, `device_name`) VALUES ('%s', '%s', '%s');",
					$escaped_user_id,
					$escaped_device_id,
					$escaped_device_id
				)
			);
			if (!$result) {
				return new Response (null, Errors::DB_QUERY_FAILED);
			}
		} else {
			$result = mysql_query (
				sprintf (
					"DELETE FROM `user_devices` WHERE `user_id` = '%s' AND `device_id` = '%s';",
					$escaped_user_id,
					$escaped_device_id
				)
			);
			if (!$result) {
				return new Response (null, Errors::DB_QUERY_FAILED);
			}
		}

		return new Response (null);
	}

	/**
	 * allow usage of device by group
	 *
	 * @param $device_id
	 * @param $group_id
	 * @param $allow
	 * @return Response
	 */
	public function allow_to_group ($device_id, $group_id, $allow) {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_super_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$escaped_group_id = $utils->escape_sql ($group_id);
		$escaped_device_id = $utils->escape_sql ($device_id);

		if ($allow) {
			$result = mysql_query (
				sprintf (
					"INSERT INTO `group_devices` (`group_id`, `device_id`) VALUES ('%s', '%s');",
					$escaped_group_id,
					$escaped_device_id
				)
			);
			if (!$result) {
				return new Response (null, Errors::DB_QUERY_FAILED);
			}
		} else {
			$result = mysql_query (
				sprintf (
					"DELETE FROM `group_devices` WHERE `group_id` = '%s' AND `device_id` = '%s';",
					$escaped_group_id,
					$escaped_device_id
				)
			);
			if (!$result) {
				return new Response (null, Errors::DB_QUERY_FAILED);
			}
		}

		return new Response (null);
	}

	/**
	 * set user's device name
	 *
	 * @param $device_id
	 * @param $user_id
	 * @param $name
	 * @return Response
	 */
	public function set_name_for_user ($device_id, $user_id, $name) {

		include_once ("db_connect.php");

		global $utils, $auth;

		$result = $utils->check_is_user ();
		if ($result->errored ()) {
			return $result;
		}

		if ($auth->get_authorized_id ()->data != $user_id) {
			$result = $utils->check_is_admin ();
			if ($result->errored ()) {
				return $result;
			}
		}

		$escaped_user_id = $utils->escape_sql ($user_id);
		$escaped_device_id = $utils->escape_sql ($device_id);
		$escaped_name = $utils->escape_sql ($name);

		$result = mysql_query (
			sprintf (
				"UPDATE `user_devices` SET `device_name` = '%s' WHERE `user_id` = '%s' AND `device_id` = '%s';",
				$escaped_name,
				$escaped_user_id,
				$escaped_device_id
			)
		);
		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		return new Response (null);
	}

	/* TODO: return object of class `UserDevice` (when class `UserDevice` is done) */
	/**
	 * extract user's device information from database row
	 *
	 * @param $row		result of `mysql_fetch_array`
	 * @return array	associative array of properties
	 */
	private function extract_user_device_info ($row) {
		return array (
			"device_specific" => $this->extract_device_info ($row),
			"user_specific" => array (
				"name" => $row["device_name"]
			)
		);
	}
}

$devices = new Devices ();
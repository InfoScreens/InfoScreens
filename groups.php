<?php

include_once ("utils.php");
include_once ("auth.php");
include_once ("x.php");

/**
 * Class Groups
 *
 * collection of groups (of users)
 */
class Groups {

	/**
	 * get group
	 *
	 * @param $id		group id
	 * @return Response	data is result of `extract_group_info` applied to queried database row
	 */
	public function get ($id) {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_super_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$escaped_id = $utils->escape_sql ($id);

		$result = mysql_query (
			sprintf (
				"SELECT * FROM `groups` WHERE `id` = '%s';",
				$escaped_id
			)
		);
		$row = mysql_fetch_array ($result);
		if (!$row) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		return new Response ($this->extract_group_info ($row));
	}

	/**
	 * create new group
	 *
	 * @return Response	created group id
	 */
	public function create () {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_super_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$name = "group";

		$escaped_name = $utils->escape_sql ($name);

		$result = mysql_query (
			sprintf (
				"INSERT INTO `groups` (`name`) VALUES ('%s');",
				$escaped_name
			)
		);
		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$id = mysql_insert_id ();

		return new Response ($id);
	}

	/**
	 * get list of groups
	 *
	 * @return Response	data is array of results from `extract_group_info` applied to all queried database rows
	 */
	public function get_list () {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_super_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$result = mysql_query ("SELECT * FROM `groups`;");

		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		$list = array ();
		while ($row = mysql_fetch_array ($result)) {
			$list[] = $this->extract_group_info ($row);
		}

		return new Response ($list);
	}

	/**
	 * set group's name
	 *
	 * @param $group_id
	 * @param $name
	 * @return Response
	 */
	public function set_name ($group_id, $name) {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_is_super_admin ();
		if ($result->errored ()) {
			return $result;
		}

		$escaped_group_id = $utils->escape_sql ($group_id);
		$escaped_name = $utils->escape_sql ($name);

		$result = mysql_query (
			sprintf (
				"UPDATE `groups` SET `name` = '%s' WHERE `id` = '%s';",
				$escaped_name,
				$escaped_group_id
			)
		);
		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		return new Response (null);
	}

	/**
	 * extract group information from database row
	 *
	 * @param $row		result of `mysql_fetch_array`
	 * @return array	associative array of properties
	 */
	private function extract_group_info ($row) {
		return array (
			"id" => $row["id"],
			"name" => $row["name"]
		);
	}
}

$groups = new Groups ();
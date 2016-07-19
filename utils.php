<?php

include_once ("x.php");
include_once ("auth.php");
include_once ("users.php");

class Utils {

	public function escape_html ($raw) {
		return htmlspecialchars ($raw, ENT_QUOTES);
	}

	public function escape_sql ($raw) {
		return mysql_escape_string ($raw);
	}

	public function escape_url ($raw) {
		return rawurlencode ($raw);
	}

	public function redirect ($url) {
		header ("Location: ".$url);
	}

	public function check_email ($email) {

		if (!filter_var ($email, FILTER_VALIDATE_EMAIL)) {
			return new Response (null, Errors::EMAIL_FORMAT_INVALID);
		}

		return new Response (null);
	}

	/* TODO: remove, when `check_user_permissions` made public */
	public function check_is_user () {

		return $this->check_user_permissions (1);
	}

	/* TODO: remove, when `check_user_permissions` made public */
	public function check_is_admin () {

		return $this->check_user_permissions (2);
	}

	/* TODO: remove, when `check_user_permissions` made public */
	public function check_is_super_admin () {

		return $this->check_user_permissions (3);
	}

	/* TODO: add named constants */
	/* TODO: make public and replace `check_is_user`, `check_is_admin`, `check_is_super_admin` */
	/**
	 * checks if current user permissions satisfy reqired permissions level
	 *
	 * @param int $level	minimal reqired permissions level, 0 - anonymous, 1 - user, 2 - admin, 3 - superadmin
	 * @return Response
	 */
	private function check_user_permissions ($level = 0) {

		global $auth, $users;

		if ($level == 0) {
			return new Response (null);
		}

		$user_id = $auth->get_authorized_id ();
		if ($user_id->errored ()) {
			return $user_id;
		}
		$user_id = $user_id->data;

		if ($level == 1) {
			return new Response (null);
		}

		$user = $users->get ($user_id);
		if ($user->errored ()) {
			return $user;
		}
		$user = $user->data;

		if (!$user["is_admin"]) {
			return new Response (null, Errors::NOT_ADMIN);
		}

		if ($level == 2) {
			return new Response (null);
		}

		if (!$user["is_super_admin"]) {
			return new Response (null, Errors::NOT_SUPER_ADMIN);
		}

		if ($level == 3) {
			return new Response (null);
		}

		return new Response (null);
	}
}

$utils = new Utils ();
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

	public function check_is_admin () {

		return $this->check_is_admin_or_super_admin ();
	}

	public function check_is_super_admin () {

		return $this->check_is_admin_or_super_admin (true);
	}

	private function check_is_admin_or_super_admin ($is_super_admin = false) {

		global $auth, $users;

		$user_id = $auth->get_authorized_id ();
		if ($user_id->errored ()) {
			return $user_id;
		}
		$user_id = $user_id->data;

		$user = $users->get ($user_id);
		if ($user->errored ()) {
			return $user;
		}
		$user = $user->data;

		if (!$user["is_admin"]) {
			return new Response (null, Errors::NOT_ADMIN);
		}

		if ($is_super_admin && !$user["is_super_admin"]) {
			return new Response (null, Errors::NOT_SUPER_ADMIN);
		}

		return new Response (null);
	}
}

$utils = new Utils ();
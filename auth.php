<?php

include_once ("utils.php");
include_once ("x.php");

class Auth {

	const STATIC_SALT = "siloponni";
	const MIN_PASSWORD_LENGTH = 8;

	public function __construct () {
		$this->session_started = FALSE;
    }

	private function require_session () {
		if (!$this->session_started) {
			$this->session_started = TRUE;
			session_start ();
		}
	}

	public function get_authorized_id () {

		$this->require_session ();

		if (!isset ($_COOKIE["auth_token"]) || !isset ($_SESSION["auth_token"]) || !$_SESSION["auth_token"] == $_COOKIE["auth_token"]) {
			return new Response (null, Errors::NOT_AUTHORIZED);
		}

		$id = $_SESSION["auth_user_id"];
		return new Response ($id);
	}

	public function is_authorized () {
		return $this->get_authorized_id ()->error == Errors::SUCCESS;
	}

	public function authorize ($email, $password) {

		include_once ("db_connect.php");

		global $utils;

		$escaped_email = $utils->escape_sql ($email);
		$password_hash = $this->hash_password ($password);
		$escaped_password_hash = $utils->escape_sql ($password_hash);

		$row = mysql_fetch_array (mysql_query ("SELECT * FROM `users` WHERE `email`='".$escaped_email."' AND `password`='".$escaped_password_hash."';"));
		$success = $row != NULL;

		if ($success) {
			$this->require_session ();
			$_SESSION["auth_user_id"] = $row["userId"];
			$token = $this->make_token ();
			$_SESSION["auth_token"] = $token;

			setcookie ("auth_token", $token, session_get_cookie_params ()["lifetime"], "/");
		}

		return $success;
	}

	public function unauthorize () {
		if ($this->is_authorized ()) {
			unset ($_SESSION["auth_user_id"]);
			unset ($_SESSION["auth_token"]);
			setcookie ("auth_token", "", time () - 3600, "/");
		}
	}

	private function hash_password ($password) {
		return sha1 (sha1 ($password).$this::STATIC_SALT);
	}

	private function make_token () {
		return base64_encode (openssl_random_pseudo_bytes (30));
	}

	public function set_user_credentials ($user_id, $email, $password) {

		include_once ("db_connect.php");

		global $utils;

		$result = $utils->check_email ($email);
		if ($result->errored ()) {
			return $result;
		}

		$result = $this->check_password ($password);
		if ($result->errored ()) {
			return $result;
		}

		$escaped_email = $utils->escape_sql ($email);
		$password_hash = $this->hash_password ($password);
		$escaped_password_hash = $utils->escape_sql ($password_hash);

		$result = mysql_query (
			sprintf (
				"UPDATE `users` SET `email` = '%s', `password` = '%s' WHERE `userId` = %d;",
				$escaped_email, $escaped_password_hash, $user_id
			)
		);
		if (!$result) {
			return new Response (null, Errors::DB_QUERY_FAILED);
		}

		return new Response (null);
	}

	public function check_password ($password) {

		if (strlen ($password) < Auth::MIN_PASSWORD_LENGTH) {
			return new Response (null, Errors::PASSWORD_TOO_SHORT);
		}

		return new Response (null);
	}
}
$auth = new Auth ();


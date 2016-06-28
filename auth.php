<?php

include_once ("utils.php");

class Auth {

	const STATIC_SALT = "siloponni";

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
		$id = null;

		if (isset ($_COOKIE["auth_token"]) && isset ($_SESSION["auth_token"]) && $_SESSION["auth_token"] == $_COOKIE["auth_token"]) {
			$id = $_SESSION["auth_user_id"];
		}

		return $id;
	}

	public function is_authorized () {
		return $this->get_authorized_id () != NULL;
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
}
$auth = new Auth ();


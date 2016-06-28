<?php

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
}

$utils = new Utils ();
<?php

abstract class Errors {
	const SUCCESS = 0;
	const DB_QUERY_FAILED = 1;
	const EMAIL_FORMAT_INVALID = 2;
	const PASSWORD_TOO_SHORT = 3;
	const NAME_IS_EMPTY = 4;
	const SURNAME_IS_EMPTY = 5;
	const AJAX_ERROR = 6;
	const UNKNOWN_ACTION = 7;
}

class Response {
	public $data, $error;
	function __construct ($data, $error = Errors::SUCCESS) {
		$this->data = $data;
		$this->error = $error;
	}
	function errored () {
		return $this->error != Errors::SUCCESS;
	}
	function to_json () {
		return json_encode (
			array (
				"error" => $this->error,
				"data" => $this->data
			)
		);
	}
}

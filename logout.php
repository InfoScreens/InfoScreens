<?php

include_once ("auth.php");
include_once ("utils.php");

$auth->unauthorize ();

$utils->redirect ("/login.php");

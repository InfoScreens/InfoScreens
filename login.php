<?php

include_once ("auth.php");
include_once ("utils.php");

abstract class LOGIN_PAGE_ERROR {
	const OK = 0;
	const EMPTY_PASSWORD = 1;
	const AUTHORIZATION_FAILED = 2;
}

$is_post = $_SERVER["REQUEST_METHOD"] == "POST";

$error = $is_post || !isset ($_GET["error"]) ? LOGIN_PAGE_ERROR::OK : $_GET["error"];

$is_authorized = $auth->is_authorized ();

$t_params = $is_post ? $_POST : $_GET;
$p_email = isset ($t_params["email"]) ? $t_params["email"] : "";

if (!$is_authorized && $is_post) {

	$p_password = isset ($_POST["password"]) ? $_POST["password"] : "";

	if (strlen ($p_password) == 0) {
		$error = LOGIN_PAGE_ERROR::EMPTY_PASSWORD;
	}

	if ($error == LOGIN_PAGE_ERROR::OK) {
		$is_authorized = $auth->authorize ($p_email, $p_password);
		if (!$is_authorized) {
			$error = LOGIN_PAGE_ERROR::AUTHORIZATION_FAILED;
		}
	}
}

if ($is_authorized) {
	$utils->redirect ("/admin.php");
} else  {
	if ($is_post) {
		$utils->redirect ("/login.php?error=".$utils->escape_url ($error)."&email=".$utils->escape_url ($p_email));
	} else {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Log in</title>
	<meta charset="utf8">
	<link type="text/css" rel="stylesheet" href="script/bootstrap-3.3.6-dist/css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/style.css">
	<link href="script/vis/dist/vis.css" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

</head>
<body>
	<div class="modal-dialog modal-sm">
		<div class="modal-content panel-primary">
			<div class="modal-header panel-heading">
				<h4 class="text-center">Log in</h4>
			</div>
			<div class="modal-body panel-body">
				<?php
		if ($error == LOGIN_PAGE_ERROR::AUTHORIZATION_FAILED) {
				?>
				<div class="alert alert-danger">Authorization failed</div>
				<?php
		}
				?>
				<form action="login.php" method="POST" role="form">
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="email" class="form-control" name="email" id="email" value="<?php echo $utils->escape_html ($p_email); ?>">
					</div>
					<div class="form-group">
						<label for="password">Password:</label>
						<input type="password" class="form-control" name="password" id="password">
					</div>
					<button type="submit" class="btn btn-primary">Login</button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
<?php

	}
}

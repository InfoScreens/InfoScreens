<?php

include_once ("auth.php");
include_once ("users.php");
include_once ("utils.php");

$is_authorized = $auth->is_authorized ();

if (!$is_authorized) {

	$utils->redirect ("/login.php");

} else {

$user_info = $users->get_info ($auth->get_authorized_id ());

if (!$user_info["is_admin"]) {
	$utils->redirect ("/admin.php");
} else {

$is_post = $_SERVER["REQUEST_METHOD"] == "POST";

if ($is_post) {

	$email = isset ($_POST["email"]) ? $_POST["email"] : "";
	$password = isset ($_POST["password"]) ? $_POST["password"] : "";
	$name = isset ($_POST["name"]) ? $_POST["name"] : "";
	$surname = isset ($_POST["surname"]) ? $_POST["surname"] : "";
	$is_admin = isset ($_POST["is_admin"]) ? $_POST["is_admin"] : "0";

	$users->create ($email, $password, $name, $surname, $is_admin);

	$utils->redirect ("/users_list.php");

} else {

$users = $users->get_list ();

?><!DOCTYPE html>
<html>
<head>
	<title>Add user</title>
	<meta charset="utf8">
	<link type="text/css" rel="stylesheet" href="script/bootstrap-3.3.6-dist/css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/style.css">
	<link href="script/vis/dist/vis.css" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	<link href="css/bootstrap-datetimepicker.css" rel="stylesheet">
	<link href="css/bootstrap-select.min.css" rel="stylesheet">

</head>
<body>
<div class="container">
	<div class="form-group">
		<a href="/users_list.php" class="btn btn-default">Users list</a>
	</div>

	<div class="form-group">

		<form action="add_user.php" method="POST" role="form">
			<table class="table table-bordered">
				<tr>
					<th>Email</th>
					<td><input type="email" name="email" required /></td>
				</tr>
				<tr>
					<th>Password</th>
					<td><input type="text" name="password" required /></td>
				</tr>
				<tr>
					<th>Name</th>
					<td><input type="text" name="name" required /></td>
				</tr>
				<tr>
					<th>Surname</th>
					<td><input type="text" name="surname" required /></td>
				</tr>
				<tr>
					<th>Permissions</th>
					<td>
						<select name="is_admin">
							<option value="0" selected>User</option>
							<option value="1">Admin</option>
						</select>
					</td>
				</tr>
			</table>
			<input type="submit" value="Create" class="btn btn-default" />
		</form>

	</div>
</div>

<script src="script/jquery-2.2.4.min.js"></script>
<script src="script/jquery-ui-1.11.4.custom/jquery-ui.js"></script>

<script src="script/moment-with-locales.js"></script>
<script src="script/bootstrap-datetimepicker.js"></script>

<script src="script/bootstrap-3.3.6-dist/js/bootstrap.js"></script>
<script src="script/bootstrap-select.min.js"></script>

<script type="text/javascript">
	$(function () {
		$('#datetimepicker').datetimepicker({
			format:'DD.MM.YYYY'
		});
		$('.selectpicker').selectpicker();
	});
</script>

</body>
</html><?php

		}
	}
}

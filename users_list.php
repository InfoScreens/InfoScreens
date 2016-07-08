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

			$action = isset ($_POST["action"]) ? $_POST["action"] : "";

			if ($action == "users_set_is_admin") {

				$user_id = isset ($_POST["user_id"]) ? $_POST["user_id"] : "";
				$is_admin = isset ($_POST["is_admin"]) ? $_POST["is_admin"] : "0";

				$users->set_info ($user_id, "is_admin", $is_admin);
			}

			$utils->redirect ("/users_list.php");

		} else {

			$users = $users->get_list ();

?><!DOCTYPE html>
<html>
	<head>
		<title>User list</title>
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
				<a href="/admin.php" class="btn btn-default">Admin panel</a>
				<a href="/add_user.php" class="btn btn-default">Create user</a>
			</div>

			<div class="form-group">
				<table class="table table-bordered">
					<tr>
						<th>Email</th>
						<th>Name</th>
						<th>Surname</th>
						<th>Permissions</th>
					</tr><?php

			foreach ($users as $user) {

					?>
					<tr>
						<td><?php echo $utils->escape_html ($user["email"]); ?></td>
						<td><?php echo $utils->escape_html ($user["name"]); ?></td>
						<td><?php echo $utils->escape_html ($user["surname"]); ?></td>
						<td>
							<form action="users_list.php" method="POST" role="form">
								<input type="hidden" name="action" value="users_set_is_admin" />
								<input type="hidden" name="user_id" value="<?php echo $utils->escape_html ($user["id"]); ?>" />
								<select name="is_admin">
									<option value="0"<?php if (!$user["is_admin"]) {echo " selected";} ?>>User</option>
									<option value="1"<?php if ($user["is_admin"]) {echo " selected";} ?>>Admin</option>
								</select>
								<input type="submit" value="Change" class="btn btn-default" />
							</form>
						</td>
					</tr><?php

			}
					?>
				</table>
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

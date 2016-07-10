<!DOCTYPE html>
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
				<a href="/add_user.html" class="btn btn-default">Create user</a>
			</div>

			<div class="form-group">

				<div id="error_text" class="alert alert-danger" style="display: none;"></div>

				<table id="users_list_table" class="table table-bordered">
					<tr>
						<th>Email</th>
						<th>Name</th>
						<th>Surname</th>
						<th>Permissions</th>
					</tr>
					<tr class="template user_info_row">
						<td class="user_info_column_email"></td>
						<td class="user_info_column_name"></td>
						<td class="user_info_column_surname"></td>
						<td>
							<form class="user_info_permission_form">
								<input type="hidden" name="user_id" />
								<select name="is_admin">
									<option value="0">User</option>
									<option value="1">Admin</option>
								</select>
								<input type="hidden" name="is_admin_old" />
								<input type="submit" value="Change" class="btn btn-default" />
							</form>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<script src="script/jquery-2.2.4.min.js"></script>
		<script src="script/jquery-ui-1.11.4.custom/jquery-ui.js"></script>

		<script src="script/moment-with-locales.js"></script>
		<script src="script/bootstrap-datetimepicker.js"></script>

		<script src="script/bootstrap-3.3.6-dist/js/bootstrap.js"></script>
		<script src="script/bootstrap-select.min.js"></script>

		<script src="script/x.js"></script>
		<script src="script/common.js"></script>

		<script type="text/javascript">
			$(function () {
				$('#datetimepicker').datetimepicker({
					format:'DD.MM.YYYY'
				});
				$('.selectpicker').selectpicker();

				check_user_authorized (true);

				function huy (event) {

					event.preventDefault ();

					var form = this;

					perform_action (
						"set_user_is_admin",
						{
							user_id: form.user_id.value,
							is_admin: form.is_admin.value
						},
						function (response) {

							if (response.errored ()) {

								$("#error_text")
									.text (get_error_text (response.error))
									.show ();
								form.is_admin.value = form.is_admin_old.value;

							} else {

								$("#error_text").hide ();
								form.is_admin_old.value = form.is_admin.value;

							}
						}
					);
				}

				perform_action (
					"get_users_list",
					null,
					function (response) {

						if (response.errored ()) {

							$("#error_text")
								.text (get_error_text (response.error))
								.show ();

						} else {

							$("#error_text").hide ();

							var row_template = $("#users_list_table tr.template.user_info_row"),
								container = row_template.parent (),
								users_list = response.data;
							for (var i in users_list) {
								var user = users_list[i],
									row = row_template
										.clone ()
										.removeClass ("template"),
									permissions = user.is_admin ? 1 : 0,
									permission_form = row.find (".user_info_permission_form")[0];

								row.find (".user_info_column_email").text (user.email);
								row.find (".user_info_column_name").text (user.name);
								row.find (".user_info_column_surname").text (user.surname);

								permission_form.user_id.value = user.id;
								permission_form.is_admin.value
									= permission_form.is_admin_old.value
									= permissions;

								$(permission_form).submit (huy);

								container.append (row);
							}
						}
					}
				);
			});
		</script>

	</body>
</html>

<!DOCTYPE html>
<html>
<head>
	<title>Admin panel</title>
	<meta charset="utf8">
	<link type="text/css" rel="stylesheet" href="script/bootstrap-3.3.6-dist/css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/style.css">
	<link href="script/vis/dist/vis.css" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <link href="css/bootstrap-datetimepicker.css" rel="stylesheet">
	<link href="css/bootstrap-select.min.css" rel="stylesheet">
	<script src="script/ckeditor/ckeditor.js"></script>
	<style>
	.fa{
		font-size:2.0em;
		}
		</style>

</head>
<body>
	<div class="row">
	<div class="col-lg-3 action-panel">
		<h3>Action panel</h3>
		<select class="form-control selectpicker" id="monSelect">
		</select>
		<br>
		<div class='input-group date' id='datetimepicker'>
                    <input type='text' class="form-control" id="date">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
        <button type="button" class="btn btn-default" style="width:100%;" id="openScheduleBtn" onclick="openSchedule()">Загрузить программу</button>
		<div class="well well-sm">
			<div class="form-group">
				<strong id="user_name"></strong>
			</div>
			<div class="form-group">
				<i id="user_email"></i>
			</div>
			<div class="form-group">
				<a href="/logout.php" class="btn btn-default">Log out</a>
				<a id="user_devices" class="btn btn-default">My devices</a>
			</div>
		</div>
		<div class="well well-sm remove_if_not_admin">
			<div class="form-group">
				<strong>Admin</strong>
			</div>
			<div class="form-group">
				<a id="group_devices" class="btn btn-default">Group devices</a>
				<a id="group_users" class="btn btn-default">Group users</a>
			</div>
		</div>
		<div class="well well-sm remove_if_not_super_admin">
			<div class="form-group">
				<strong>Super admin</strong>
			</div>
			<div class="form-group">
				<a id="groups" class="btn btn-default" href="groups.html">Groups</a>
				<a id="devices" class="btn btn-default" href="all_devices.html">Devices</a>
			</div>
		</div>

	  </div>

	<div class="col-lg-8 work-area">
		<h3>Work area</h3>

		<!--<div class="element"></div>-->
		<div class="element pdf" data-title="Имя файла" style=""><!--<img src="files/thumbnails/pres.pdf.jpg">--></div>
		<div class="element add-element"></div>

		<form id="addFiles" name="addFiles">
			<input type="file"  id="addFile" name="file">
			</form>





	  </div>

	<div id="context-menu" class="context-menu">
	    <ul class="context-menu__items">
	      <li class="context-menu__item">
	        <a href="#" id="addFileBtn" class="context-menu__link" data-action="View"><i class="fa fa-file-o" aria-hidden="true"></i> Add file</a>
	      </li>
	      <li class="context-menu__item">
	        <a href="#" id="addAdvertBtn" class="context-menu__link" data-action="Edit"><i class="fa fa-file-text-o"></i> Add text</a>
	      </li>
	      <li class="context-menu__item">
	        <a href="#" id="addTickerBtn" class="context-menu__link" data-action="Edit"><i class="fa fa-comment-o"></i> Add ticker</a>
	      </li>
	      <li class="context-menu__item">
	        <a href="#" class="context-menu__link" data-action="Delete"><i class="fa fa-times"></i> Delete Task</a>
	      </li>
	    </ul>
	  </div>

	</div>

	<div class="row">
		<div class="col-lg-12">
			<div id="timeline">
				
			  </div>
		  </div>
	  </div>


	<div id="time-setting">
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 "><span class="text-center">Время начала <br> (Start time)</span></div>
			<div class="col-lg-6 col-md-6 col-sm-6 "><span class="text-center">Время конца<br>(End time)</span></div>		
		</div>
		<form role="form" class="form-group ">
			<div class="row" >
				<div class="col-lg-6 col-md-6 col-sm-6 form-inline">
					<input class="form-control" type="text" maxlength="2" size="2" id="startHour" value="00">
					<span>:</span>
					<input class="form-control" type="text" maxlength="2" size="2" id="startMinutes" value="00">
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 form-inline">
					<input class="form-control" type="text" maxlength="2" size="2" id="endHour" value="00">
					<span>:</span>
					<input class="form-control" type="text" maxlength="2" size="2" id="endMinutes" value="00">
				</div>		
			</div>
			<div class="row">,
			<button type="submit" class="btn btn-primary btn-sm btn-block" id="addItemBtn">Добавить/Add</button>

			<!--<button type="button" class="btn .btn-info .btn-block" id="addItem">Добавить/Add</button>-->
			</div>
		</form>
	</div>

	

	<div class="textEditor" id="advertEditor">
		<textarea id="textEditor" ></textarea>
			<script >
				CKEDITOR.replace("textEditor",{
					height:400
				});
				</script>
		<button class="btn btn-primary pull-right" id="saveAdvert">Add advert</button>
	</div>

	<div id="tickerEditor">
		<textarea   rows="3" ></textarea>
		<button class="btn btn-primary pull-right" id="saveTicker">Add ticker</button>
	</div>

	<div class="overlay"></div>




	<script src="script/jquery-2.2.4.min.js"></script>
	<script src="script/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
	
	<script src="script/vis/dist/vis.js"></script>
	<script src="script/moment-with-locales.js"></script>
			<script src="script/bootstrap-datetimepicker.js"></script>

	<script src="script/bootstrap-3.3.6-dist/js/bootstrap.js"></script>
	<script src="script/bootstrap-select.min.js"></script>
	
	<script type="text/javascript">
            $(function () {
                $('#datetimepicker').datetimepicker({
                	format:'YYYY-MM-DD',
                	defaultDate: date
                });
                $('.selectpicker').selectpicker();

				perform_action (
					"get_current_user_info",
					null,
					function (response) {
						if (response.errored ()) {
							window.location = "login.php";
						} else {
							var user = response.data,
								user_id = user.id;

							$("#user_name").text (user.name + " " + user.surname);
							$("#user_email").text (user.email);
							$("#user_devices")[0].href = "devices_of_user.html?user_id=" + window.escape (user.id);

							var temp = $(".remove_if_not_admin"),
								temp2 = $(".remove_if_not_super_admin");
							if (user.is_admin) {
								temp.removeClass ("remove_if_not_admin");

								$("#group_devices")[0].href = "devices_of_group.html?group_id=" + window.escape (user.group_id);
								$("#group_users")[0].href = "users_list.html?group_id=" + window.escape (user.group_id);

								if (user.is_super_admin) {
									temp2.removeClass ("remove_if_not_super_admin");
								} else {
									temp2.remove ();
								}
							} else {
								temp.remove ();
								temp2.remove ();
							}

							perform_action (
								"get_list_of_user_devices",
								{
									user_id: user_id
								},
								function (response) {
									if (response.errored ()) {
										alert (response.error + ", " + get_error_text (response.error));
									} else {
										var devices = response.data,
											container = $("#monSelect");
										for (var i in devices) {
											var device = devices[i];
											container.append (
												$("<option>")
													.text (device.user_specific.name)
													.val (device.device_specific.id)
											);
										}
										container.selectpicker ("refresh");
									}
								}
							);
						}
					}
				);
            });
        </script>
    
	<script src="script/script.js"></script>
	<script src="script/x.js"></script>
	<script>
	

	</script>

</body>
</html>

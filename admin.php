<?php

include_once ("auth.php");
include_once ("users.php");
include_once ("utils.php");

$is_authorized = $auth->is_authorized ();

if (!$is_authorized) {

	$utils->redirect ("/login.php");

} else {

	$user_info = $users->get ($auth->get_authorized_id ()->data)->data;

?>
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
			<option value="1"> Mon 1 </option>
			<option value="2"> Mon 2 </option>
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
				<strong><?php echo $utils->escape_html ($user_info["name"]." ".$user_info["surname"]); ?></strong>
			</div>
			<div class="form-group">
				<i><?php echo $utils->escape_html ($user_info["email"]); ?></i>
			</div>
			<div class="form-group">
				<a href="/logout.php" class="btn btn-default">Log out</a>
			</div>
		</div>
		<?php

	if ($user_info["is_admin"]) {

		?><div class="well well-sm">
			<div class="form-group">
				<strong>Admin</strong>
			</div>
			<div class="form-group">
				<a href="/users_list.html" class="btn btn-default">Manage users</a>
			</div>
		</div><?php

	}

		?>

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
	        <a href="#" id="addVideoBtn" class="context-menu__link" data-action="View"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i> Add presentation</a>
	      </li>
	      <li class="context-menu__item">
	        <a href="#" class="context-menu__link" data-action="Edit"><i class="fa fa-file-video-o"></i> Add video</a>
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
			<div class="row">
			<button type="submit" class="btn btn-primary btn-sm btn-block" id="addItemBtn">Добавить/Add</button>

			<!--<button type="button" class="btn .btn-info .btn-block" id="addItem">Добавить/Add</button>-->
			</div>
		</form>
	</div>
	<div id="overlay"></div>


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
            });
        </script>
    
	<script src="script/script.js"></script>
	<script>
	

	</script>

</body>
</html>
<?php
}

<?php

$email = $_POST['email'];
$password = $_POST['password'];


include "db_connect.php";
$sql_query = "SELECT * FROM 'users' WHERE 'email' = '".$email."'";
$result = mysql_query($sql_query);
while($user = mysql_fetch_array($result)){
	if(sha1(sha1($password)."siloponni") != $user['password']){
		header("Location: login.php");
	}
}


?>



<!DOCTYPE html>
<html>
<head>
	<title>Admin panel</title>
	<meta charset="utf8">
	<link type="text/css" rel="stylesheet" href="script/bootstrap-3.3.6-dist/css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/style.css">
	<link href="script/vis/dist/vis.css" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
	<link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet">

</head>
<body>
<?php
//echo sha1(sha1("4815162342")."siloponni");
?>
	<div class="row">
	<div class="col-lg-3 action-panel">
		<h3>Action panel</h3>
		<select class="form-control selectpicker">
			<option> Mon 1 </option>
			<option> Mon 2 </option>
		</select>
		<div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>

	  </div>

	<div class="col-lg-8 work-area">
		<h3>Work area</h3>

		<div class="element"></div>
		<div class="element add-element"></div>


	  </div>

	<div id="context-menu" class="context-menu">
	    <ul class="context-menu__items">
	      <li class="context-menu__item">
	        <a href="#" class="context-menu__link" data-action="View"><i class="fa fa-eye"></i> View Task</a>
	      </li>
	      <li class="context-menu__item">
	        <a href="#" class="context-menu__link" data-action="Edit"><i class="fa fa-edit"></i> Edit Task</a>
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


	<script src="script/jquery-2.2.4.min.js"></script>
	<script src="script/jquery-ui-1.11.4.custom/jquery-ui.js"></script>
	
	<script src="script/vis/dist/vis.js"></script>
	<script src="script/script.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
			<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

	<script src="script/bootstrap-3.3.6-dist/js/bootstrap.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
	
	<script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker();
                $('.selectpicker').selectpicker();
            });
        </script>


</body>
</html>

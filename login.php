


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
				<form action="admin.php" method="POST" role="form">
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="email" class="form-control" name="email" id="email">
					</div>
					<div class="form-group">
						<label for="password">Password:</label>
						<input type="password" class="form-control" name="password" id="password">
					</div>
					<button type="submit" class="btn btn-primary">Login</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

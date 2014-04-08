<?php

session_start();

if(isset($_POST['user']) && isset($_POST['password'])){
	if($_POST['user'] == 'login' && $_POST['password'] == 'password'){
		$_SESSION['loggedin'] = 'yes';
		header('Location: index.php');
	}
}

?>
<!doctype html>
<html>
<head>
	<title>Website Lab Editor</title>
	<link rel="stylesheet" href="css/normalize.min.css">
	<link rel="stylesheet" href="css/admin.css">
</head>
<body>
	<div class="container">
		<div class="login">
			<a class="logo-link" href="http://www.websitelab.co.nz" target="_blank"><img class="logo" src="http://www.websitelab.co.nz/img/logo.png" alt="Website Lab"></a>
			<form method="post">
				<div class="field">
					<label>Username</label>
					<input class="inputfield" type="text" name="user">
				</div>
				<div class="field">
					<label>Password</label>
					<input class="inputfield" type="password" name="password">
				</div>
				<div class="login-field">
					<input type="submit" value="Login to Editor">
				</div>
			</form>
			<p class="footnote">Website Lab Editor &copy; v0.2 - 2014</p>
		</div>
	</div>
</body>
</html>
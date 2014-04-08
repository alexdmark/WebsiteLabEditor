<?php

session_start();

if($_GET['logout'] == 'yes'){
	unset($_SESSION['loggedin']);
}

if($_SESSION['loggedin'] != 'yes'){
	header('Location: login.php');
}

//do some maintenance 
//delete backups from root folder
if ($handle = opendir('../')) {
	while (false !== ($entry = readdir($handle))) {
		$filetype = substr($entry, -11);
		if ($filetype == 'backup.html') {
			//delete the file	
			unlink('../'.$entry);				
		}
	}
	closedir($handle);
}

?>
<!doctype html>
<html>
<head>
	<title>Website Lab Editor</title>
	<link rel="stylesheet" href="css/normalize.min.css">
	<link rel="stylesheet" href="css/admin.css">
	<link rel="shortcut icon" href="http://www.websitelab.co.nz/favicon.ico" />
</head>
<body>
	<div class="header-container">
		<header class="clearfix">
			<a class="logo-link logo-container" href="http://www.websitelab.co.nz" target="_blank"><img class="logo" src="http://www.websitelab.co.nz/img/logo.png" alt="Website Lab"></a>
			<nav class="top-nav">
				<span>Page Manager</span> | 
				<a href="../" target="_blank">View Site</a> | 
				<a href="index.php?logout=yes">Logout</a>
			</nav>
		</header>
	</div>
	<div class="container clearfix">
		<section class="pages-container">
			<h1>Your Pages:</h1>
			<ul class="pages">
			<?php
			
			if ($handle = opendir('../')) {
				while (false !== ($entry = readdir($handle))) {
					$filetype = substr($entry, -4);
					if ($filetype == 'html') {
						echo '<li>
						'.$entry.' |
						<a href="../'.$entry.'" target="_blank">View Page</a> | 
						<a href="../editme.php?page='.$entry.'">Edit Page</a> | 
						<a class="view-backups" data-page-name="'.$entry.'">View Backups</a>
						</li>';
					}
				}
				closedir($handle);
			}

			?>
			</ul>
		</section>
		<section class="help-container">
			<h1>FAQs & Help:</h1>
			<p><strong>Q: Where is my home page?</strong></p>
			<p>A: Your home page is called index.html</p>
		</section>
	</div>
	
	<div class="container" id="backups-container">
		<section>
			<h1>Backups of <span id="backup-page-name"></span></h1>
			<ul id="backups"></ul>
		</section>
	</div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<script src="js/admin.js"></script>
</body>
</html>
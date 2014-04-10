<?php

session_start();

//config settings
$uploadpath = "uploads/";

//die if not logged in
if($_SESSION['loggedin'] != 'yes'){
	die('login');
}

//save html changes to page
if($_POST['action'] == 'save-page'){

	$file = '../'.$_POST['name'];
	$backup = "backups/".$_POST['name'].".".time();
	
	//delete old backups here TODO

	//first create backup
	copy($file, $backup);
	
	require('simple_html_dom.php');

	// Create DOM from URL or file
	$html = file_get_html('../'.$_POST['name']);
	
	$changes = json_decode($_POST['WLEchanges']);
	
	//cycle through changes to be made
	foreach($changes as $change){

		$element = $html->find('#'.$change->id);	
		
		//if action is update, update the whole html
		if($change->WLEaction == 'update'){
			$element[0]->outertext = $change->WLEhtml;
		}
		//if action is change-src, just change the src attr
		elseif($change->WLEaction == 'change-src'){
			$element[0]->setAttribute('src', $change->WLEhtml);
		}
		//if action is add-css, add css to page
		elseif($change->WLEaction == 'add-css'){
		
			$style = $html->find('style[class=WLEcustomcss]');
			//if WLEcustomcss element already exists
			if($style){
				
				$style[0]->innertext = $style[0]->innertext.$change->WLEhtml;
				
			}
			//if WLEcustomcss doesn't exist yet
			else {
				$head = $html->find('head');
				$head[0]->innertext = $head[0]->innertext.'<style class="WLEcustomcss">'.$change->WLEhtml.'</style>';
			}
		}
		//if action is delete, delete the element
		elseif($change->WLEaction == 'delete'){
			$element[0]->outertext = '';
		}
	
	}
	
	if($html->save('../'.$_POST['name'])){
		echo 'success';
	}

}

//save new image
if(isset($_FILES['new-img'])){

	$name = $_FILES['new-img']['name'];
	$size = $_FILES['new-img']['size'];
	$tmp = $_FILES['new-img']['tmp_name'];
	move_uploaded_file($tmp, '../'.$uploadpath.$name); //Stores the image in the uploads folder
	
	echo $uploadpath.$name;
}

//get backups
if($_POST['action'] == 'get-backups'){
	
	//create array of backups
	$backups = array();

	if ($handle = opendir('backups/')) {
		while (false !== ($entry = readdir($handle))) {
		
			//split pages by .
			$backupPage = explode(".", $entry);
			$page = explode(".", $_POST['page']);
			
			if ($backupPage[0] == $page[0]) {
				
				//add backup to array
				$backups[] = $entry;
				
			}
		}
		closedir($handle);
		
		//put backups in order (alphabetically descending) - use sort for alphabetical
		rsort($backups);
		
		foreach($backups as $backup){
			//get datetime
			$backupPage = explode(".", $backup);
			$datetime = date("d/m/Y - g:i:s a", $backupPage[2]);
			//echo backups in order
			echo '<li>'.$datetime.' | <a href="process.php?action=view-backup&page='.$backup.'" target="_blank">View Backup</a> | <a class="restore-backup" data-page-name="'.$backup.'">Restore</a> | <a class="delete-backup" data-page-name="'.$backup.'">Delete</a></li>';
		}
	}
	
}


//view backup
if($_GET['action'] == 'view-backup'){

	$temppage = "../".$_GET['page'].".backup.html";

	//copy and rename backup
	copy("backups/".$_GET['page'], $temppage);
	
	//then redirect to backup
	header("Location: ".$temppage);

}

//restore backup
if($_POST['action'] == 'restore-backup'){

	$page = explode(".", $_POST['page']);
	
	//first backup current page
	copy('../'.$page[0].'.html', 'backups/'.$page[0].'.html.'.time());

	//then restore selected backup
	rename("backups/".$_POST['page'], '../'.$page[0].'.html');

}

//delete backup
if($_POST['action'] == 'delete-backup'){

	unlink('backups/'.$_POST['page']);

}

?>
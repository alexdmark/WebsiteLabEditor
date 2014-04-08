function getBackups(backuppage){

	$.ajax({
		type: "POST",
		url: "process.php",
		data: { action: 'get-backups', page: backuppage }
	}).done(function( results ) {
	
		$('#backups-container').fadeOut(function(){
			//add backup results
			$('#backup-page-name').html(backuppage);
			$('#backups').html(results);
		});
		
		//and fade backups in
		$('#backups-container').fadeIn();
	});
	
}

//view backups function
$(".view-backups").click(function(){

	var backuppage = $(this).attr('data-page-name');
	getBackups(backuppage);

});

//restore backup function
$("body").delegate(".restore-backup", "click", function(){

	var backuppage = $(this).attr('data-page-name');
	$.ajax({
		type: "POST",
		url: "process.php",
		data: { action: 'restore-backup', page: backuppage }
	}).done(function( results ) {
		alert('Backup has been restored!');
		location.reload();
	});

});

//delete backup function
$("body").delegate(".delete-backup", "click", function(){

	var backuppage = $(this).attr('data-page-name');
	$.ajax({
		type: "POST",
		url: "process.php",
		data: { action: 'delete-backup', page: backuppage }
	}).done(function( results ) {
		alert('Backup has been deleted!');
		backuppage = backuppage.split(".");
		getBackups(backuppage[0]+'.'+backuppage[1]);
	});

});
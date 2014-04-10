<?php

session_start();

if($_SESSION['loggedin'] != 'yes'){
	die('login');
}

header('Content-type: application/javascript');

?>

/***** WebsiteLabEditor v1.4 *****/

//set initial vars
var edittext = false;
var editimages = false;
var deletemode = false;
var prevFocus;
var prevHover;
var WLEarray = [];

//check still logged in
function checkAuth(){
			
	//check if logged in   		
	$.ajax({
		type: "POST",
		url: "editme/process.php",
		data: { action: 'checklogin' }
	}).done(function( msg ) {
		if(msg == 'login'){
			alert("Sorry, you've been logged out due to inactivity. Please login again.");
			window.location='/editme';
		}
	});
}

//enable or disable links
function toggleLinks(state){
	if(state == 'disable'){
		//disable links
		$('a').each(function(){
	    	var alink = $(this).attr('href');
	    	$(this).attr('data-temp-link', alink);
	    	$(this).attr('href','');
	    });
	}
	else {
		//enable links
	    $('a').each(function(){
	    	var alink = $(this).attr('data-temp-link');
	    	$(this).attr('href', alink);
	   		$(this).removeAttr('data-temp-link');
	   	});
	}
}

//toggle text editor
function toggleTextEditor(state){

	//check user is logged in so they don't make heaps of changes and then can't save them
    checkAuth();
	
	if(state == 'enable'){
		//disable links
		toggleImageEditor('disable');
		toggleDeleteMode('disable');
		toggleLinks('disable');
		
		//make editable and show which elements they can edit
		$(".WLEeditable:not(img)").attr("contenteditable","true").css('outline','#ffe767 dashed 2px');
				
		$("#WLEedittext").html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Stop Editing');
	    $("#WLEedittext").css('backgroundColor', '#e54627');
		edittext = true;
	}
	else {
		//enable links
		toggleLinks('enable');
		
		//disable editable and remove outline
		$(".WLEeditable:not(img)").attr("contenteditable","false").css('outline','');
				
		$("#WLEedittext").html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Text');
	    $("#WLEedittext").css('backgroundColor', '#67c036');
		edittext = false;
	}
	
}

//toggle image editor
function toggleImageEditor(state){

	//check user is logged in so they don't make heaps of changes and then can't save them
    checkAuth();
	
	if(state == 'enable'){
		//disable links
		toggleTextEditor('disable');
		toggleDeleteMode('disable');
		toggleLinks('disable');
				
		$("#WLEeditimages").html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Stop Editing');
	    $("#WLEeditimages").css('backgroundColor', '#e54627');
		editimages = true;
	}
	else {
		//enable links
		toggleLinks('enable');
				
		$("#WLEeditimages").html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Pictures');
	    $("#WLEeditimages").css('backgroundColor', '#67c036');
		editimages = false;
	}
	
}

//toggle delete mode
function toggleDeleteMode(state){

	//check user is logged in so they don't make heaps of changes and then can't save them
    checkAuth();
	
	if(state == 'enable'){
		//disable links
		toggleTextEditor('disable');
		toggleImageEditor('disable');
		toggleLinks('disable');
		
		//outline deletable sections
		$('.WLEdelete').css('outline','#F00 dotted 2px');
				
		$("#WLEdeletemode").html('<img src="editme/delete-button.png" style="width:20px;padding-right:8px;">Stop Deleting');
	    $("#WLEdeletemode").css('backgroundColor', '#e54627');
		deletemode = true;
	}
	else {
		//enable links
		toggleLinks('enable');
		
		//takeaway outline
		$('.WLEdelete').css('outline','');
				
		$("#WLEdeletemode").html('<img src="editme/delete-button.png" style="width:20px;padding-right:8px;">Delete Something');
	    $("#WLEdeletemode").css('backgroundColor', '#67c036');
		deletemode = false;
	}
	
}

function deleteElement(id){

	var answer = confirm("Are you sure you want to delete this element?");
	
	if(answer == true){
	
		//store the change in the WLEarray
		item = {}
		item ["id"] = id;
		item ["WLEaction"] = 'delete';
				
		WLEarray.push(item);

		//then remove the element from the current page
		$('#'+id).fadeOut();
		
	}
	
}

function addCSS(id, css){
	//store the change in the WLEarray
	item = {}
	item ["id"] = id;
	item ["WLEaction"] = 'add-css';
	item ["WLEhtml"] = css;
				
	WLEarray.push(item);
}

	
		
	
		//create edit and save buttons
		var WLEbuttons = '<div id="WLEbuttons" style="position:fixed;top:50%;margin-top:-72px;left:0;z-index:99999;">';
		WLEbuttons += '<button style="margin-bottom:0.6em;color:white;border:none;background:#34a3cf;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;" onclick="window.location=\'/editme\';"><img src="editme/back-button.png" style="width:20px;padding-right:8px;">Page Manager</button>';
		WLEbuttons += '<br><button style="margin-bottom:0.6em;color:white;border:none;background:#67c036;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;" id="WLEedittext"><img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Text</button>';
		WLEbuttons += '<br><button style="margin-bottom:0.6em;color:white;border:none;background:#67c036;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;" id="WLEeditimages"><img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Pictures</button>';
		WLEbuttons += '<br><button style="margin-bottom:0.6em;color:white;border:none;background:#67c036;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;" id="WLEdeletemode"><img src="editme/delete-button.png" style="width:20px;padding-right:8px;">Delete Something</button>';
		WLEbuttons += '<br><button id="WLEfontup">Font +</button><button id="WLEfontdown">Font -</button>';
		WLEbuttons += '<br><button onclick="document.execCommand(\'undo\');">Undo</button><button onclick="document.execCommand(\'redo\');">Redo</button>';
		WLEbuttons += '<br><button style="margin-top:0.6em;color:white;border:none;background:#34a3cf;font:16px sans-serif;padding:10px;cursor:pointer;" id="WLEsavepage"><img src="editme/save-button.png" style="width:20px;padding-right:8px;">Save Page</button>';
		WLEbuttons += '</div>';
		
		$('body').append(WLEbuttons);
		
		/*************** Image Editing Functions ***************/
		
		//when an editable element is hovered over, store it for later use so we don't change the opacity too early
		$("body").delegate(".WLEeditable, .WLEbackgroundimage, .WLEslideshow, .WLEdelete", "mouseenter", function(){
		
			//set prevFocus for potential element changes (e.g. font-size, bold)
			prevHover = $(this);
			
		});
		
		//if the edit image button is hovered over, keep it there
		$("body").delegate(".WLEchangeimagebutton, .WLEeditslideshowbutton, .WLEdeletebutton", "mouseenter", function(){
			$(this).show();
			prevHover.css('opacity', '0.5');
		});
		
		//then remove it
		$("body").delegate(".WLEchangeimagebutton, .WLEeditslideshowbutton, .WLEdeletebutton", "mouseleave", function(){
			$(this).hide();
			prevHover.css('opacity', '1');
		});
		
		
		//mouseenter event for normal & background images
		$("body").delegate("img.WLEeditable, .WLEbackgroundimage", "mouseenter", function(){
		
			//if editimages == yes
			if(editimages === true){
    		
    			var imgid = $(this).attr('id');
    			
    			//fade img
    			$(this).css('opacity', '0.5');
    			
    			//if change image form doesn't exist yet
    			if($('#'+imgid+'-form').length == 0){
    			
    				//is it a background image
    				if($(this).hasClass('WLEbackgroundimage')){
	    				var imgtype = 'background-image';
    				}
    				else {
	    				var imgtype = 'normal-image';
    				}
    				
    				//create button
					$('body').append('<form id="'+imgid+'-form" class="WLEimageform"><input style="display:none;" class="WLEimageuploadbutton" type="file" data-img-id="'+imgid+'" data-img-type="'+imgtype+'" id="img'+imgid+'"><img src="editme/edit.png" style="display:none;width:40px;cursor:pointer;position:absolute;z-index:9999;" class="WLEchangeimagebutton" data-img-id="'+imgid+'"></form>');
	    			
    			}
    		
				//show the button
				$("img.WLEchangeimagebutton[data-img-id="+imgid+"]").show();
    		
				//position the button
				$("img.WLEchangeimagebutton[data-img-id="+imgid+"]").position({
					my: "center",
					at: "center",
					of: $("#"+imgid)
				});
	    	}
    	});
    	
    	//mouseenter event for slideshows
		$(".WLEslideshow").mouseenter(function(){
			if(editimages === true){
			
				var slideshowid = $(this).attr('id');
    			
    			//fade slideshow
    			$(this).css('opacity', '0.5');
    			
    			//if slideshow popup doesn't exist yet
    			if($('#'+slideshowid+'-poupup').length == 0){
    			
    				var WLEimages = '';
    				
    				//first get all imgs
    				$('#'+slideshowid+' img').each(function() {
						WLEimages += '<img class="WLEslideshowimage" data-original-id="'+$(this).attr('id')+'" src="'+$(this).attr('src')+'" style="max-width:300px;max-height:300px;"><form id="'+$(this).attr('id')+'-form" class="WLEimageform"><input class="WLEimageuploadbutton" type="file" data-img-id="'+$(this).attr('id')+'" data-img-type="normal-image" id="img'+$(this).attr('id')+'"></form><br>';
					});
					
					//next create the popup
					$('body').append('<div id="'+slideshowid+'-poupup" style="display:none;position:fixed;top:50%;left:50%;width:600px;height:400px;margin-left:-300px;margin-top:-200px;z-index:99999;overflow:scroll;background:white;border:5px solid black;">'+WLEimages+'</div>');
    				
    				//then create the edit slideshow button
    				$('body').append('<img src="editme/edit.png" style="display:none;width:40px;cursor:pointer;position:absolute;z-index:9999;" class="WLEeditslideshowbutton" data-slideshow-id="'+slideshowid+'">');
    			}
    			
    			//show the button
				$("img.WLEeditslideshowbutton[data-slideshow-id="+slideshowid+"]").show();
    		
				//position the button
				$("img.WLEeditslideshowbutton[data-slideshow-id="+slideshowid+"]").position({
					my: "center",
					at: "center",
					of: $("#"+slideshowid)
				});
    		
			}
		});
		
		//function for when slideshow edit button is clicked
		$("body").delegate(".WLEeditslideshowbutton", "click", function(){
			$('#'+$(this).attr('data-slideshow-id')+'-poupup').show();
		});
    	
    	//hide slideshow eidt button on mouseleave
    	$("body").delegate(".WLEslideshow", "mouseleave", function(){
    		
    		var slideshowid = $(this).attr('id');
    		
	    	$(this).css('opacity', '1');
    		
    		//hide the button
			$("img.WLEeditslideshowbutton[data-slideshow-id="+slideshowid+"]").hide();
	    	
    	});
    	
    	//hide normal and background image edit buttons on mouseleave
    	$("body").delegate("img.WLEeditable, .WLEbackgroundimage", "mouseleave", function(){
    		
    		var imgid = $(this).attr('id');
    		
	    	$(this).css('opacity', '1');
    		
    		//hide the button
			$("img.WLEchangeimagebutton[data-img-id="+imgid+"]").hide();
	    	
    	});
    	    	
    	//if edit image button is clicked, we need to fake a click to the real input file browser button
    	$("body").delegate(".WLEchangeimagebutton", "click", function(){
			$("#img"+$(this).attr("data-img-id")).click();
		});

		//when a new image is chosen for upload
		$("body").delegate(".WLEimageuploadbutton", "change", function(){

        	//save image
			var oldimgid = $(this).attr("data-img-id");
			var imgtype = $(this).attr("data-img-type");
			var file = this.files[0];
			var formData = new FormData();
			formData.append( "new-img", file );
			
			//upload the file via ajax
			$.ajax({
            	type:"POST",
				url: "editme/process.php",
				data:formData,
				cache:false,
				contentType: false,
				processData: false,
				success:function(data){
					if(imgtype == 'normal-image'){
						$("#"+oldimgid).attr("src", data+ '?' +new Date().getTime() );
						//add change-src data-action so the script knows to only change the src
						$("#"+oldimgid).attr('data-action', 'change-src');
					}
					if(imgtype == 'background-image'){
						//create temporary css for current page
						$('head').append('<style>#'+oldimgid+' {background-image:url("'+data+ '?' +new Date().getTime()+'")}</style>');
						//add new css to WLEarray
						addCSS(oldimgid, '#'+oldimgid+' {background-image:url("'+data+ '?' +new Date().getTime()+'");}');
					}
                	
				},
				error: function(data){
                	alert("Sorry, we ran into an issue, please contact support.");
				}
			});
		});
		
		/****** delete event *******/
		
		//mouseenter event for slideshows
		$("body").delegate(".WLEdelete", "mouseenter", function(){
			if(deletemode === true){
				$(this).css('opacity', '0.5');
				
				var elementid = $(this).attr('id');
				
				//create delete button if it doesn't exist
				if($('#'+elementid+'-delete-button').length == 0){
				
					//create button
					$('body').append('<img src="editme/delete-button.png" style="display:none;cursor:pointer;background:black;padding:0.4em;" id="'+elementid+'-delete-button" class="WLEdeletebutton" onclick="deleteElement(\''+elementid+'\');">');
				
				}
				
				//show the button
				$('#'+elementid+'-delete-button').show();
    		
				//position the button
				$('#'+elementid+'-delete-button').position({
					my: "center",
					at: "center",
					of: $("#"+elementid)
				});
			}
		});
		
		//mouseleave for deleteable element
		$("body").delegate(".WLEdelete", "mouseleave", function(){
		
			var elementid = $(this).attr('id');
    		
	    	$(this).css('opacity', '1');
    		
    		//hide the button
			$('#'+elementid+'-delete-button').hide();
		
		});
    	
    	//when an editable element that isn't an image is focused, store it for later style maniplulations
		$(".WLEeditable:not(img)").focus(function() {
		
			//set prevFocus for potential element changes (e.g. font-size, bold)
			prevFocus = $(this);
			
			//add data-action attribute to update so we know to save changes later
			$(this).attr('data-action', 'update');
		});
    	
    	//font increase
    	$("#WLEfontup").click(function(){
    		var WLEfontsize = prevFocus.css('font-size');
    		newfontsize = parseInt(WLEfontsize) + 2 + "px";
	    	prevFocus.css('font-size', newfontsize);
    	});
    	
    	//font decrease
    	$("#WLEfontdown").click(function(){
    		var WLEfontsize = prevFocus.css('font-size');
    		newfontsize = parseInt(WLEfontsize) - 2 + "px";
	    	prevFocus.css('font-size', newfontsize);
    	});
    	
    	//edit image click 
    	$("#WLEeditimages").click(function(){
    	
    		if(editimages === false){
    			toggleImageEditor('enable');
			}
			else {
				toggleImageEditor('disable');
			}
	    	
    	});
    		
		//edit text function
    	$("#WLEedittext").click(function(){
    		
    		if(edittext === false){
	    		toggleTextEditor('enable');
    		}
    		else {
	    		toggleTextEditor('disable');
    		}
    	});
    	
    	//delete function
    	$("#WLEdeletemode").click(function(){
    		
    		if(deletemode === false){
	    		toggleDeleteMode('enable');
    		}
    		else {
	    		toggleDeleteMode('disable');
    		}
    	});
		
		//save function
    	$("#WLEsavepage").click(function(){
    	
    		//first lets remove WLE helper elements & attributes
			$(".WLEeditable").removeAttr("contenteditable").css('outline','');
			$("img").removeAttr("data-img-id");
			if(edittext === true || editimages === true){
				toggleLinks('enable');
    		}
    		
    		//create json of all content to be sent to server
			$(".WLEeditable[data-action]").each(function() {

				var id = $(this).attr("id");
				var WLEaction = $(this).attr("data-action");
				
				//remove WLE attributes before storing html changes in array
				$(this).removeAttr('data-action');
				
				//if action is change-src
				if(WLEaction == 'change-src'){
					var WLEhtml = $(this).attr("src");
				}
				//if action is update
				if(WLEaction == 'update'){
					var WLEhtml = $(this)[0].outerHTML;
				}
				
				item = {}
				item ["id"] = id;
				item ["WLEhtml"] = WLEhtml;
				item ["WLEaction"] = WLEaction;
				
				WLEarray.push(item);
			});

			//then send the changes to the server    		
	    	$.ajax({
				type: "POST",
				url: "editme/process.php",
				data: { action: 'save-page', name: WLEpagename, WLEchanges: JSON.stringify(WLEarray) }
			}).done(function( msg ) {
				if(msg == 'success'){
					alert("Your changes have been saved.");
					location.reload();
				}
				else {
					alert(msg);
					//window.location='/editme';
				}
			});
			
    	});
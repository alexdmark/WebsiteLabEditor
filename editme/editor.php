<?php

session_start();

if($_SESSION['loggedin'] != 'yes'){
	die('login');
}

?>

/***** WebsiteLabEditor v1.4 *****/

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
function enableLinks(state){
	if(state === false){
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

	
		//set initial vars
		var edittext = false;
		var editimages = false;
		var prevFocus;
	
		//create edit and save buttons
		var WLEbuttons = '<div id="WLEbuttons" style="position:fixed;top:50%;margin-top:-72px;left:0;z-index:99999;">';
		WLEbuttons += '<button style="margin-bottom:0.6em;color:white;border:none;background:#34a3cf;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;" onclick="window.location=\'/editme\';"><img src="editme/back-button.png" style="width:20px;padding-right:8px;">Page Manager</button>';
		WLEbuttons += '<br><button style="margin-bottom:0.6em;color:white;border:none;background:#67c036;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;" id="WLEedittext"><img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Text</button>';
		WLEbuttons += '<br><button style="margin-bottom:0.6em;color:white;border:none;background:#67c036;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;" id="WLEeditimages"><img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Pictures</button>';
		WLEbuttons += '<br><button id="WLEfontup">Font +</button><button id="WLEfontdown">Font -</button>';
		WLEbuttons += '<br><button onclick="document.execCommand(\'undo\');">Undo</button><button onclick="document.execCommand(\'redo\');">Redo</button>';
		WLEbuttons += '<br><button style="margin-top:0.6em;color:white;border:none;background:#34a3cf;font:16px sans-serif;padding:10px;cursor:pointer;" id="WLEupdatepage"><img src="editme/save-button.png" style="width:20px;padding-right:8px;">Save Page</button>';
		WLEbuttons += '</div>';
		
		$('body').append(WLEbuttons);
		
		//if the edit button is hovered over, keep it there
		$("body").delegate(".WLEchangeimagebutton", "mouseover", function(){
			$(this).show();
		});
		
		//then remove it
		$("body").delegate(".WLEchangeimagebutton", "mouseout", function(){
			$(this).hide();
		});
		
		
		//on mouseover show the edit button
		$("img.WLEeditable, .WLEbackgroundimage").mouseover(function(){
		
			//if editimages == yes
			if(editimages === true){
    		
    			var imgid = $(this).attr('id');
    			
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
    	
    	//and hide on mouseout
    	$("img.WLEeditable, .WLEbackgroundimage").mouseout(function(){
    		
    		var imgid = $(this).attr('id');
    		
    		//hide the button
			$("img.WLEchangeimagebutton[data-img-id="+imgid+"]").hide();
	    	
    	});
    	    	
    	//if edit is clicked, we need to fake a click
    	$("body").delegate(".WLEchangeimagebutton", "click", function(){
			$("#img"+$(this).attr("data-img-id")).click();
		});

		//when new image is chosen
		$("body").delegate(".WLEimageuploadbutton", "change", function(){

        	//save image
			var oldimgid = $(this).attr("data-img-id");
			var imgtype = $(this).attr("data-img-type");
			var file = this.files[0];
			var formData = new FormData();
			formData.append( "new-img", file );
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
						//create new css
						$('head').append('<style id="style-for-'+oldimgid+'" data-action="new-css" class="WLEcustomcss">#'+oldimgid+' {background-image:url('+data+ '?' +new Date().getTime()+')}</style>');
					}
                	
				},
				error: function(data){
                	alert("Sorry, we ran into an issue, please contact support.");
				}
			});
		});
    	
    	//when an editable element is focused, define what we want to do with it
		$(".WLEeditable").focus(function() {
		
			//set prevFocus for potential element changes (e.g. font-size, bold)
			prevFocus = $(this);
			
			//add replace data-action attr for editable sections that aren't images
			if(edittext == true && $(this).prop("tagName") != 'IMG'){
				$(this).attr('data-action', 'replace');
			}
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
    	
    	//edit image function
    	$("#WLEeditimages").click(function(){
    	
    		if(editimages === false){
    			//disable links
				enableLinks(false);
				
				$(this).html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Stop Editing');
	    		$(this).css('backgroundColor', '#e54627');
				$("#WLEedittext").attr('disabled', 'true');
				editimages = true;
			}
			else {
				//enable links
				enableLinks(true);
				
				$(this).html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Pictures');
	    		$(this).css('backgroundColor', '#67c036');
				$("#WLEedittext").removeAttr('disabled');
				editimages = false;
			}
	    	
    	});
    		
		//edit text function
    	$("#WLEedittext").click(function(){
    	
    		//check user is logged in so they don't make heaps of changes and then can't save them
    		checkAuth();
    		
    		if(edittext === false){
	    		$(".WLEeditable").attr("contenteditable","true").css('outline','#ffe767 dashed 2px');
	    		//disable links
	    		enableLinks(false);
	    		
	    		$(this).html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Stop Editing');
	    		$(this).css('backgroundColor', '#e54627');
	    		$("#WLEeditimages").attr('disabled', 'true');
				edittext = true;
    		}
    		else {
	    		$(".WLEeditable").attr("contenteditable","false").css('outline','');
	    		//show links again
	    		enableLinks(true);
	    		
	    		$(this).html('<img src="editme/edit-button.png" style="width:20px;padding-right:8px;">Edit Text');
	    		$(this).css('backgroundColor', '#67c036');
	    		$("#WLEeditimages").removeAttr('disabled');
				edittext = false;
    		}
    	});
		
		//save function
    	$("#WLEupdatepage").click(function(){
    	
    		//first lets remove WLE helper elements & attributes
			$(".WLEeditable").removeAttr("contenteditable").css('outline','');
			$("img").removeAttr("data-img-id");
			if(edittext === true || editimages === true){
				enableLinks(true);
    		}
    		
    		//create json of all editable content
    		jsonObj = [];
			$(".WLEeditable[data-action], .WLEcustomcss[data-action]").each(function() {

				var id = $(this).attr("id");
				var WLEaction = $(this).attr("data-action");
				
				//remove WLE attributes before storing html changes in array
				$(this).removeAttr('data-action');
				
				//if action is change-src
				if(WLEaction == 'change-src'){
					var WLEhtml = $(this).attr("src");
				}
				//if action is change-background
				if(WLEaction == 'new-css'){
					var WLEhtml = $(this).html();
				}
				//if action is replace
				if(WLEaction == 'replace'){
					var WLEhtml = $(this)[0].outerHTML;
				}
				
				item = {}
				item ["id"] = id;
				item ["WLEhtml"] = WLEhtml;
				item ["WLEaction"] = WLEaction;
				
				jsonObj.push(item);
			});

			//then send the changes to the server    		
	    	$.ajax({
				type: "POST",
				url: "editme/process.php",
				data: { action: 'save-page', name: WLEpagename, WLEchanges: JSON.stringify(jsonObj) }
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
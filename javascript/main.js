SessionService = {
	set(key, value) {
    return sessionStorage.setItem(key, value);
	},
	get(key) {
	    return sessionStorage.getItem(key);
	},
	destroy(key) {
	    return sessionStorage.removeItem(key);
	},
	hasKey(key) {
	    return sessionStorage.getItem(key) != undefined;
	}	
};


function deleteFile(fileName){
	var tmp = confirm("Are you sure you want to delete this file.");
	if(tmp){
		window.location = './api/deleteFile.php?fileName='+fileName;
	};
}


$('document').ready(function(){
	var location = window.location.pathname.split('/').slice(-1)[0];
	var loginRequiredPath = [
		'index.html', 'updateprofile.html', 'changepass.html',
		'upload.html'
	];

	var logoutRequirePath = ['login.html'];

	var isLoggedIn = false;
	var currUserData = {};

	$.get('./api/isLoggedIn.php', function(data){
		var resp = jQuery.parseJSON(data);
		if(resp.isLoggedIn){
			isLoggedIn = true;
			SessionService.set('userLoggedIn', true);
			getUserData();
		}else{
			SessionService.destroy('userLoggedIn');
		}
		redirectionHandler();
	});

	function redirectionHandler(){
		if(isLoggedIn){
			if(logoutRequirePath.indexOf(location) != -1){
				window.location = 'index.html';
			}
		}else{
			if(loginRequiredPath.indexOf(location) != -1){
				window.location = 'login.html';
			}
		}
	}

	function getUserData(){
		$.get('./api/userData.php', function(data){
			var resp = jQuery.parseJSON(data);
			if(resp.status){
				currUserData.userData = resp.userData;
				currUserData.files = resp.files;
				$('#first-name').html(currUserData.userData.firstName);
				if(currUserData.files.length > 0){
					$('#noFileDiv').css({'display': 'none'});
					var userDocumentDiv = $('#fileDiv');
					jQuery.each(currUserData.files, function (i, field){
						userDocumentDiv.find('.document-type').html(field.type);
						userDocumentDiv.find('.document-description').html(field.description);
						userDocumentDiv.find('.download a').attr("href", 
							"./api/download.php?fileName="+field.tmpName+"&actualName="+field.name);
					userDocumentDiv.find('.delete a').attr("onclick", "deleteFile('"+field.tmpName+"')");
						if(i==0){
							$('#user_documents_container').html('');
							$('#user_documents_container').append(userDocumentDiv.html());
						}else{
							$('#user_documents_container').append(userDocumentDiv.html());
						}	
					});
				}else{
					$('#fileDiv').css({'display': 'none'});
				}
			}
		});
	}

	$('#loginForm').submit(login);
	$('#logout').click(logout);
	$('#signupForm').submit(register);
	$('#changePassForm button').submit(changePass);
	$('#updateProfileForm').submit(updateProfile);
	$('#fileUploadForm').submit(uploadFile);


	function login(){
		var user = {};
		var fields =  $('#loginForm').serializeArray();
		jQuery.each(fields, function(i, field){
			user[field.name] = field.value;
		});
		$.post('./api/login.php', user, function(data){loginCB(data)});
		return false;
	}

	function loginCB(data){
		var resp = jQuery.parseJSON(data);
		if(resp.loginStatus){
			window.location = './index.html';
		}else{
			if(resp.message != undefined){
				alert(resp.message);
				$('#loginForm input').val('');
			}else{
				jQuery.each(resp.errors, function(i, field){
				$('#loginForm input[name="'+field.name+'"]').css({"border-color":"red"});
			});
			}
		}
	}

	function logout(){
		$.get('./api/logout.php');
		SessionService.destroy('userLoggedIn');
		window.location = './login.html';
	}

	function register(){
		var newUser = {};
		var fields =  $('#signupForm').serializeArray();
		jQuery.each(fields, function(i, field){
			newUser[field.name] = field.value;
		});
		$.post('./api/signup.php', newUser, function(data){registerCB(data)});
		return false;
	}

	function registerCB(data){
		var resp = jQuery.parseJSON(data);
		if(resp.registered){
			alert('Success. You can now login');
			window.location = './login.html';
		}else{
			if(resp.errors.server != undefined){
				alert(resp.errors.server);
			}else{
				jQuery.each(resp.errors, function(i, field){
					$('#signupForm input[name="'+field.name+'"]').css({"border-color":"red"});
					if(field['unique'] != undefined){
						alert("Email already exists");
					}
				});
			}
			$('#signupForm input[type="password"]').val("");
		}
	}

	function uploadFile(){
		var formData = new FormData($('#fileUploadForm')[0]);
		$.ajax({
        url: './api/fileupload.php',
        type: 'POST',
        data: formData,
        async: true,
        success: function (data) {
            uploadFileCB(data)
        },
        cache: false,
        contentType: false,
        processData: false
    });
    return false;
	}

	function uploadFileCB(data){
		var resp = jQuery.parseJSON(data);
		if(resp.fileUploaded){
			alert("File successfully uploaded.");
			window.location = 'upload.html';
		}else{
			jQuery.each(resp.errors, function(i, field){
				$('#fileUploadForm input[name="'+field.name+'"]').css({"border-color":"red"});
			});
		}
	}



	function updateProfile(){
		var profileData = {};
		var fields =  $('#updateProfileForm').serializeArray();
		jQuery.each(fields, function(i, field){
			profileData[field.name] = field.value;
		});
		$.post('./api/profile.php', profileData, function(data){updateProfileCB(data)});
		return false;
	}

	function updateProfileCB(data){
		var resp = jQuery.parseJSON(data);
		if(resp.profileUpdated){
			alert("Profile successfully updates");
			window.location = 'index.html';
		}else{
			jQuery.each(resp.errors, function(i, field){
				$('#updateProfileForm input[name="'+field.name+'"]').css({"border-color":"red"});
			});
		}
	}

	function changePass(){
		var passwordData = {};
		var fields =  $('#changePassForm').serializeArray();
		jQuery.each(fields, function(i, field){
			passwordData[field.name] = field.value;
		});
		$.post('./api/changepass.php', passwordData, function(data){changePassCB(data)});
		return false;
	}

	function changePassCB(data){
		var resp = jQuery.parseJSON(data);
		if(resp.passChangeStatus){
			alert("Password successfully changed.");
			window.location = 'index.html';
		}else{
			jQuery.each(resp.errors, function(i, field){
				$('#changePassForm input[name="'+field.name+'"]').css({"border-color":"red"});
			});
		}
	}


	

});


// section for ui stuff
$('document').ready(function(){
	$('.signup-container .form-group input').focusout(function(){
			if($(this)[0].value != ''){
				$(this).addClass('has-value');
			}else{
				$(this).removeClass('has-value');
			}
	});

	$('.signup-container .form-group input').each(function(index){
			if($(this)[0].value != ''){
				$(this).addClass('has-value');
			}else{
				$(this).removeClass('has-value');
			}
	});	
})






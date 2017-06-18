<?php 

	header('Content-Type', 'application/json');

	require_once './core/init.php';

	$user = new User();

	$response = array();
	$response["loginStatus"] = false;

	if($user->isLoggedIn()){
		$response = array('loginStatus'=>true);
	}

	if(Input::exists()){
		$validate = new Validate();
		$validate->validate($_POST, array(
			'email'=> array('label'=>'Email', 'required'=>true),
			'password'=> array( 'label'=>'Password', 'required'=>true)
			));

		if($validate->isValid()){
			$login = $user->login(Input::get('email'), Input::get('password'));
			if($login['success'] == true){
				$response["loginStatus"] = true;
			}else{
				$response["message"] = array($login['message']);
			}
		}else{
			$response["errors"]  = $validate->errors();
		}
	}

	echo json_encode($response);

 ?>

<?php 

	header('Content-type', 'application/json');
	
	require_once './core/init.php';

	$response = array("profileUpdated"=>false);

	$user = new User();

	if($user->isLoggedIn()){

		$user_data = $user->data();

		if(Input::exists()){
			$validate = new Validate();
			$validate->validate($_POST, array(
				'firstName'=>array('label'=>'First Name', 'required'=>true, 'minlength'=>3, 'maxlength'=>20),
				'lastName'=>array('label'=>'Last Name', 'maxlength'=>20),	
				)
			);
			if($validate->isValid()){
				try {
					$updateData = array(
						'firstName' => Input::get('firstName'),
						'lastName' => Input::get('lastName')
						);
					$user->update($updateData, array("id", "=", $user_data['id']));
					$user = new User();
					$userData = $user->data();
					$response['profileUpdated'] = true;	
				} catch (Exception $e) {
					$response['errors'] = array("server"=>"Problem while uploading file. Please try after some time.");
				}
			}else{
				$response["errors"] = $validate->errors();
			}
		}
	}

	echo json_encode($response);

 ?>
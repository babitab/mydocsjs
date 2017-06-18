<?php 
	
	require_once './core/init.php';

	$user = new User();
	if($user->isLoggedIn()){
		if(Input::exists('get')){
			$user_data = $user->data();
			$folder = $user_data['id'];
			$file_name = Input::get('fileName');
			$file_actual_name = Input::get('actualName');
			if($file_name != '' and $file_actual_name != ''){
				$file = new File($folder, $file_name);
				$file->download($file_actual_name);
			}
		}
	}

 ?>
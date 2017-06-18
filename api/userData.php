<?php 

	header('Content-type', 'application/json');

	require_once './core/init.php';

	$response = array('status'=>false);

	$user = new User();

	if($user->isLoggedIn()){
		$response['status'] = true;
		$data = $user->data();
		unset($data["password"]);
		$response['userData'] = $data;
		$files = $user->files();
		$response['files'] = $files == null ? array() : $files;
	}

	echo json_encode($response);

 ?>
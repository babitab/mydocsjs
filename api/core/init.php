<?php 
	
	session_start();

	// define globals for our site
	$GLOBALS['config'] = array(
		'mysql'=>array(
				'host'=>'localhost',
				'username'=>'root',
				'password'=>'',
				'dbname'=>'mydocs'
			),
		'session_name'=>'userId',
		'user_upload_dir_path'=> './../documents/users',
		'file_max_size'=>1000000
		);

	// add functions
	require_once 'functions/general.php';

	// add classes with autoload
	spl_autoload_register(function($class){
		require_once 'classes/'.$class.'.php';
	});

	

 ?>
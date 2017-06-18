<?php 
/**
* This class handles input related oprations
*/
class Input{
	
	// function to check whether input values exists or not
	public static function exists($type = 'post'){
		switch ($type) {
			case 'post':
				return isset($_POST) and !empty($_POST);
				break;
			case 'get':
				return isset($_GET) and !empty($_GET);
				break;
			case 'files':
				return isset($_FILES) and !empty($_FILES);
				break;	
			default:
				return false;
				break;
		}
	}

	// function to return the value for a key from inputs
	public static function get($key){
		if(isset($_POST[$key])){
			return $_POST[$key];
		}else if(isset($_GET[$key])){
			return $_GET[$key];
		}
		return '';
	}
	
}
 ?>
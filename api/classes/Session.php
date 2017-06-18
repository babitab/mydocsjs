<?php 
/**
* This classes handles Session related methods
*/
class Session{
	
	public static function get($key){
		return $_SESSION[$key];
	}

	public static function set($key, $value){
		return $_SESSION[$key] = $value;
	}

	public static function delete($key){
		if(self::exists($key)){
			unset($_SESSION[$key]);
		}
	}

	public static function exists($key){
		return isset($_SESSION[$key]);
	}


	// to send a flash message to user
	public static function flash($key, $message = ''){
		if(self::exists($key)){
			$message = self::get($key);
			self::delete($key);
			return $message;
		}else{
			self::set($key, $message);
		}

	}

}
 ?>
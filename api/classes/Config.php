<?php 
/**
* This class handles configuration for our site
*/
class Config{
	
	// function to retrive value for a path
	public static function get($path = null){
		if($path){
			$config = $GLOBALS['config'];
			$path = explode('/', $path);

			// get the value
			foreach ($path as $key) {
				if(isset($config[$key])){
					$config = $config[$key];
				}else{
					return false;
				}
			}
			return $config;
		}
		return false;
	}
}
 ?>
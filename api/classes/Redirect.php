<?php 
/**
* This classes handles redirections
*/
class Redirect{

	public static function to($location = null){
		if($location){
			if(is_numeric($location)){
				switch ($location) {
					case 404:
						header('HTTP/1.0 404 Not Found');
						include 'include/errors/404.php';
						break;
					
					default:
						break;
				}
			}else{
				header('Location: '.$location);
				exit();
			}

		}
	}
	
}
 ?>
<?php 

/**
* This class handles for file upload, download, delete
*/
class File {
	
	private $_folderName, $_fileName;

	public function __construct($folder_name, $file_name){
		$this->_folderName = Config::get('user_upload_dir_path').'/'.$folder_name;
		$this->_fileName = $file_name;
	}

	public function upload($tmp_location){
		if(!is_dir($this->_folderName)){
			mkdir("$this->_folderName");
			chmod("$this->_folderName", 0755);
		}
		if(move_uploaded_file($tmp_location, $this->_folderName.'/'.$this->_fileName)){
			return true;
		}
		return false;
	}

	public function download($actual_file_name){
		$file_path = $this->_folderName.'/'.$this->_fileName;
		if(file_exists($file_path)){
			header("Content-Disposition: attachment; filename=" . urlencode($actual_file_name));   
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Description: File Transfer");            
			header("Content-Length: " . filesize($file_path));
			flush(); // this doesn't really matter.
			$fp = fopen($file_path, "r");
			while (!feof($fp))
			{
			    echo fread($fp, 65536);
			    flush(); // this is essential for large downloads
			} 
			fclose($fp);
		}
	}

	public function delete(){
		$file_path = $this->_folderName.'/'.$this->_fileName;
		if(file_exists($file_path)){
			if(unlink($file_path)){
				return true;
			}
		}
		return false;
	}
}	
 ?>
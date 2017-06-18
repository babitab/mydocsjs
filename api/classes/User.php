<?php 

/**
* This class handles user interations
*/
class User{
	
	private $_db, $_sessionName, $_isLoggedIn, $_data, $_files;

	// constructor to create a user for it's uservalue 
	public function __construct($user = null){
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get('session_name');
		if($user){
			// create data for a specific user
			if($this->find($user)){
				$tmp = $this->data();
				$userId = $tmp['id'];
				$this->getFiles($userId);
			}


		}else{
			// get data for current user
			if(Session::exists($this->_sessionName)){
				$user = Session::get($this->_sessionName);
				if($this->find($user)){
					$tmp = $this->data();
					$userId = $tmp['id'];
					$this->getFiles($userId);
					$this->_isLoggedIn = true;
				}else{
					$this->_isLoggedIn = false;
				}
			}
		}
	}

	// create a user with given field values
	public function create($fields = array()){
		if(!$this->_db->insert('users', $fields)){
			throw new Exception("We are facing problems while creating your account. Please try after some time.");
		}
	}

	// to update data for the user
	public function update($fields = array(), $where){
		if(!$this->_db->update('users', $fields, $where)){
			throw new Exception("We are facing some problems upating your profile.");
		}
	}

	// to insert a file for the user
	public function addFile($fields = array()){
		if(!$this->_db->insert('documents', $fields)){
			throw new Exception("Problem while uploading file. Please try after some time.");
		}
	}

	// to delete a file for the user
	public function deleteFile($where = array()){
		if(!$this->_db->delete('documents', $where)){
			throw new Exception("Problem while uploading file. Please try after some time.");
		}
	}

	// login a user with a given username and password
	public function login($email, $password){
		$user = $this->find($email);
		if($user){
			$tmp = $this->data();
			if($tmp['password'] === md5($password)){
				Session::set($this->_sessionName, $tmp['id']);
				return array('success'=>true);	
			}else{
				return array('success'=>false, 'message'=>'Incorrect password.');
			}
		}else{
			return array('success'=>false, 'message'=>'No user registered with this email. Have you registered?');
		}
	}


	// function to logout
	public function logout(){
		if(Session::exists($this->_sessionName)){
			Session::delete($this->_sessionName);
		}
	}

	// finds an user and if exists, gets it data and sets it to _data
	public function find($user = null){
		if($user){
			$field = (is_numeric($user)) ? 'id' : 'email';
			$data = $this->_db->get('users', array($field, "=", $user));
			if($data->count()){
				$this->_data = $data->first();
				return true;
			}		
		}
		return false;
	}

	// function to retrieve files for the user
	public function getFiles($userId = null){
		if($userId and is_numeric($userId)){
			$data = $this->_db->get('documents', array('userId', "=", $userId));
			if($data->count()){
				$this->_files = $data->results();
				return true;
			}
		}
		return false;
	}

	// retrive data for the user
	public function data(){
		return $this->_data;
	}

	public function files(){
		return $this->_files;
	}

	// does user exists
	public function exists(){
		return !empty($this->_data);
	}

	// is user logged in
	public function isLoggedIn(){
		return $this->_isLoggedIn;
	}

}
 ?>
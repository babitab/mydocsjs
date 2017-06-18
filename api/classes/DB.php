<?php 

/**
* This class handles our database interaction
*/
class DB{
	
	public static $_instance = null;

	private $_mysql, $_error = false, $_count = 0, $_results, $_query;


	private function __construct(){
		$this->_mysql = new mysqli(
			Config::get('mysql/hostname'), 
			Config::get('mysql/username'),
			Config::get('mysql/password'),
			Config::get('mysql/dbname')
		);
		if($this->_mysql->connect_errno){
			die($this->_mysql->connect_error);	
		}else{	
			// echo "successfully connected to database";
		}
	}

	public static function getInstance(){
		if(!isset(self::$_instance)){
			self::$_instance = new DB();
		}
		return self::$_instance;
	}

	// function to perform a query on database
	public function query($query){
		$this->_error = false;
		if($this->_query = $this->_mysql->prepare($query)){
			if($result  = $this->_mysql->query($query)){
				if(!is_bool($result)){
					$this->_results =  $result->fetch_all( MYSQLI_ASSOC);
					$this->_count = count($this->_results);
				}	
			}else{
				echo $this->_mysql->error;
				$this->_error = true;
			}
		}else{
			echo $this->_mysql->error;
		}
		return $this;
	}


	// function to perform an insert on a table with given fiels parameters
	public function insert($table, $fields = array()){
		$keys = array_keys($fields);
		$values = array_values($fields);

		$columnKeys = "(`". implode('`, `', $keys) ."`)";
		$columnValues = "('". implode("', '", $values). "')";

		$query = "INSERT INTO `{$table}` {$columnKeys} VALUES {$columnValues}";


		if(!$this->query($query)->error()){
			return true;
		}
		return false;
	}

	public function update($table, $fields, $where){
		if(count($where) === 3){
			$operators = array('=', '>', '<','>=','<=');
			$operator = $where[1];
			if(in_array($operator, $operators)){
				$updateString = array();
				$where_key = $where[0];
				$where_value = is_numeric($where[2]) ?  $where[2] : "'".$where[2]."'";
				foreach ($fields as $key => $value) {
					if(is_numeric($value)){
						$updateString[] = "`".$key.'` = '.$value;
					}else{
						$updateString[] = "`".$key."` = '".$value."'";
					}
				}
				$updateString = implode(',', $updateString);

				$query = "UPDATE `{$table}` SET {$updateString} WHERE {$where_key} {$operator} {$where_value}";
				//echo $query;	
				if(!$this->query($query)->error()){
					return true;	
				}
			}
		}
		return false;
	}

	// delete from table, where
	public function delete($table, $where){
		if(count($where) === 3){
			$operators = array('=', '>', '<','>=','<=');
			$operator = $where[1];
			if(in_array($operator, $operators)){
				$updateString = array();
				$where_key = $where[0];
				$where_value = is_numeric($where[2]) ?  $where[2] : "'".$where[2]."'";

				$query = "DELETE FROM `{$table}` WHERE {$where_key} {$operator} {$where_value}";
				//echo $query;	
				if(!$this->query($query)->error()){
					return true;	
				}
			}
		}
		return false;
	}

	// function to perform an given action 
	// for a given table with given conditions 
	public function action($action, $table, $where = array()){
		if(count($where) === 3){
			$operators = array('=', '>', '<','>=','<=');
			$operator = $where[1];
			if(in_array($operator, $operators)){
				$field = $where[0];
				$value = $where[2];
				if(is_numeric($value)){
					$query = "{$action} FROM {$table} WHERE {$field} {$operator} {$value}";
				}else{
					$query = "{$action} FROM {$table} WHERE {$field} {$operator} '{$value}'";	
				}

				if(!$this->query($query)->error()){
					return $this;
				}else{
					return false;
				}
			}
		}
	}

	public function get($table, $where){
		return $this->action('SELECT *', $table, $where);
	}
	

	public function results(){
		return $this->_results;
	}

	public function first(){
		$data = $this->results();
		return $data[0];
	}

	public function count(){
		return $this->_count;
	}

	public function error(){
		return $this->_error;
	}
}
 ?>
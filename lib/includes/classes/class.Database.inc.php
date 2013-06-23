<?php

class Database {

	public $table    = "users";

	protected $user     = "root";
	protected $password = "root";
	protected $db       = "AWU";
	protected $host     = "localhost";
	protected $result;
	protected $tmp_result;
	
	//execute sql query statement
	public function execute_sql($query) {
	
		$mysqli = new mysqli($this->host, $this->user, $this->password, $this->db);
		$query = $mysqli->real_escape_string($query);
		$mysqli->query($query);
		$mysqli->close();
	}
	
	//returns array of one result row or 2D array of all returned rows
	public function get_all_results($query) {
	
		$mysqli = new mysqli($this->host, $this->user, $this->password, $this->db);
		if ($result = $mysqli->query($query)) {
				$i=0;
				while ($row = $result->fetch_assoc()) {
					$this->result[$i] = $row;
					$i++;	
				}
			$mysqli->close();
			if (count($this->result) > 1) {
				return $this->result;
			} else {
				return $this->result[0];
			}
		}
		else echo " MYSQL QUERY FAILED";
	}

	//returns string or assosciative array of strings
	public function clean($string){
		$mysqli = new mysqli($this->host, $this->user, $this->password, $this->db);
		$new_string_array;
		if(is_array($string)){
			foreach($string as $string_array_key => $string_array_value){
				$string_array_value = $this->clean_string($string_array_value, $mysqli);
				$new_string_array[$string_array_key] = $string_array_value;
			}
			$string = $new_string_array;
		}
		else $string = $this->clean_string($string, $mysqli);
		$mysqli->close();
		return $string;
	}

	//series of cleans to be perfomed on one string
	protected function clean_string($string, &$mysqli){
		$string = htmlspecialchars($string);
		//$string = $mysqli->real_escape_string($string);
		return $string;
	}
}

?>
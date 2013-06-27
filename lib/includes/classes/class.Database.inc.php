<?php

class Database {

	public static $table    = "users";

	protected static $user     = "root";
	protected static $password = "root";
	protected static $db       = "AWU";
	protected static $host     = "localhost";
	protected static $mysqli;

	//initialize the database connection
	public static function init_connection(){
		self::$mysqli = new mysqli(self::$host, self::$user, self::$password, self::$db);
	}

	//close the database connection
	public static function close_connection(){
		self::$mysqli->close();
	}
	
	//execute sql query statement. Used for INSERT and UPDATE mostly. Returns false if query fails
	public static function execute_sql($query) {
		if(self::$mysqli->query($query)) echo "QUERY SUCCEEDED!";
		else echo self::$mysqli->error;
	}
	
	//returns array of one result row if one result was found or 2D array of all returned rows if multiple were found
	public static function get_all_results($query) {
		$result_to_return = array(); //maybe this shouldnt be like this...
		if ($result = self::$mysqli->query($query)) {
				$i=0;
				while ($row = $result->fetch_assoc()) {
					$result_to_return[$i] = $row;
					$i++;	
				}
			if (count($result_to_return) > 1) {
				return $result_to_return;
			} 
			else if(count($result_to_return) == 1) {
				return $result_to_return[0];
			} 
			else return false; //there were no results found
		}
		else echo " MYSQL QUERY FAILED";
	}

	//returns string or assosciative array of strings
	public static function clean($string){
		$new_string_array;
		if(is_array($string)){
			foreach($string as $string_array_key => $string_array_value){
				$string_array_value = self::clean_string($string_array_value);
				$new_string_array[$string_array_key] = $string_array_value;
			}
			$string = $new_string_array;
		}
		else $string = self::clean_string($string, self::$mysqli);
		return $string;
	}

//------------------------------------------------------------------------------
//HELPERS

	//series of cleans to be perfomed on one string
	protected static function clean_string($string){
		$string = htmlspecialchars($string);
		$string = self::$mysqli->real_escape_string($string);
		return $string;
	}
}

?>
<?php

class Database {

	public static $table    = "users";
	public static $organization_table   = "organizations";
	public static $root_dir_link = "http://localhost:8888/";

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
		if(self::$mysqli->query($query)) return true;
		else echo self::$mysqli->error;
		return false;
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

	//takes a MySQL query and returns a 1D indexd array of results.
	//i.e. it can only be used with SELECT statement that returns results of only one column
	//returns array on success and false on failure.
	//called from add_list_to_organization_table() and get_results_as_JSON()
	public static function get_results_as_numerical_array($query, $column_name){
		if($results = self::get_all_results($query)){
			if(!isset($results[0])) $results = array($results); //wraps in array if $result is 1D
			$numerical_array = array();
			foreach($results as $result_row){
				$numerical_array[] = $result_row[$column_name];
			}
			return $numerical_array;
		}else return false;
	}

	//returns string or assosciative array of strings
	//mainly for $_POST and $_GET
	public static function clean($string){
		if(isset($string) && !empty($string)){
			$new_string_array;
			//if the string is actually an assoc array
			if(is_array($string)){
				foreach($string as $string_array_key => $string_array_value){
					if($string_array_key == 'media' ||
					   $string_array_key == 'tags') $string_array_value = self::format_list_for_db($string_array_value);
					else if($string_array_key == 'organizations') $string_array_value = self::format_list_for_db($string_array_value, false);
					else if($string_array_key == 'email') $string_array_value = strtolower($string_array_value);
					//$string_array_value = self::clean_string($string_array_value);
					$new_string_array[$string_array_key] = $string_array_value;
				}
				$string = $new_string_array;
			}
			//else just clean it
			//else $string = self::clean_string($string, self::$mysqli);
			return $string;
		}
		else return false; //nothing was passed as an argument
	}

//------------------------------------------------------------------------------
//HELPERS

	//formats lists like media and tags from POST to be comma-space delimited per our sites standard 
	//called inside clean()
	public static function format_list_for_db($string, $toLowerCase=true){
		$string = ($toLowerCase) ? strtolower($string) : $string;
		$string = rtrim($string, ",");
		$array = explode(",", $string);
		$new_array = array();
		foreach($array as $value){
			$new_array[] = trim($value);
		}
		$string = implode(", ", $new_array);
		return $string;
	}

	//series of cleans to be perfomed on one string
	protected static function clean_string($string){
		$string = htmlspecialchars($string);
		$string = self::$mysqli->real_escape_string($string);
		return $string;
	}

	//returns 82 char bycript password string when unhased string is passed in and
	//if two parameters are passed it returns a boolean checking if they match
	//adapted from harry at simans dot net post at http://php.net/manual/en/function.crypt.php
	//instead of putting the salt at the end the salt is prepended to the beginning of the hashed password <-- NO LONGER TRUE
	public static function hasher($unhashed_password, $encoded_data = false) { 
	  $strength = "08"; 
	  //if encrypted data is passed, check it against input ($unhashed_password) 
	  if ($encoded_data) {
	    if (substr($encoded_data, 0, 60) == crypt($unhashed_password, "$2a$".$strength."$".substr($encoded_data, 60))) return true; 
	    else return false;  
	  } 
	  else { 
	  //make a salt and hash it with input, and add salt to end 
	  $salt = ""; 
	  for ($i = 0; $i < 22; $i++) { 
	    $salt .= substr("./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", mt_rand(0, 63), 1); 
	  } 
	  //return 82 char string (60 char hash & 22 char salt) 
	  return crypt($unhashed_password, "$2a$".$strength."$".$salt) . $salt;
	}

	}
}

?>
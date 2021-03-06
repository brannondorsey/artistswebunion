<?php
require_once("class.PrivateAPI.inc.php");
require_once("class.RelationalAlgorithm.inc.php");
require_once("class.User.inc.php");

class ContentOutput{
	protected $api;
	public function __construct(){
		$this->api = new PrivateAPI();
	}

	public static function commas_to_list($string) {
		$raw_output = explode(",", $string);
		$output = array();
		foreach ($raw_output as $list_item) {
			$output[] = trim($list_item);
		}
		return $output;

		//old function:
		//$output = explode(",", $string);
		//return $output;
	}

	//confirm signed in before calling this
	public function output_profile($user_id){
		$search_array = array('id' => $user_id,
							  'limit' => 1);
		$search_obj = json_decode($this->api->get_JSON_from_GET($search_array));
		return $search_obj;
	}

	//NOW TAKES API PARAMETERS ARRAY
	public function output_search_results($search_array){
		$search_obj = json_decode($this->api->get_JSON_from_GET($search_array));
		return $search_obj;
	}

	public function output_highest_liked_users($numb_results) {
		$search_array = array('order_by' => 'likes',
							  'limit' => $numb_results,
							  'flow' => 'DESC');
		$search_obj = json_decode($this->api->get_JSON_from_GET($search_array));
		return $search_obj;
	}

	public function output_related_users($user_id){
		$rel = new RelationalAlgorithm();
		return json_decode($rel->get_related_users($user_id));
	}

	//returns total number of results from an assoc array of api parameters
	//note: pass in search array unaltered from how it will be searched
	public function total_numb_results($search_array){
		$search_array['count_only'] = true;
		if(array_key_exists('limit', $search_array)) unset($search_array['limit']);
		if(array_key_exists('page', $search_array)) unset($search_array['page']);
		$obj = json_decode($this->api->get_JSON_from_GET($search_array));
		return $obj->data[0]->count;
	}

	//removes http:// and www. from the front of urls. Returns string
	public function format_url_for_display($url){
		$url = str_replace("http://", "", $url);
		$url = str_replace("www.", "", $url);
		return $url;
	}

	#etc...
}
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cloudfare MK
 *
 * @package			Cloudflare MK
 * @author			Michelle Kondou http://www.michellekondou.me
 * @copyright 		Copyright (c) 2016 Michelle Kondou
 */

class Cloudflare_mk {

	// ee parameters
	var $site_id 			= '';
	var $has_settings		= 0;
	// cloudflare api parameters
	var $cloudflare_email 	= "";
	var $cloudflare_key 	= "";
	var $cloudflare_domain 	= "";

	function Cloudflare_mk($site_id) {

		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

		require('cloudflare_api.php');

		// start session
		if (session_id() == "") 
		{
			session_start(); 
		}

		// init
		$this->site_id = $site_id;

		if(!$this->set_cloudflare_api()) { 

			return null; 

		} else {

			$this->has_settings = 1;

		}

	}


	// ========================================================
	// PUBLIC METHODS
	// ========================================================

	/**
	 * Just checking if the cloudflare credentials (email and api_key) supplied are valid. 
	 * If they are valid then a cloudflare response is returned.
	 * This will be used when updating the DB email and key to check if a connection to cloudflare is being made.
	 * If not then the user hasn't supplied the right credentials.
	*/
	public function valid_credentials($email, $key){
 		
		$cf_api 	= new cloudflare_api($email, $key);
		$response 	= $cf_api->get_zone_data();
		if(empty($response)){
			return 0;
		} else {
			return 1;
		} 
 
	}
	/** 
	 * Get the domain names associated with an account.
	*/
	public function get_zone_names() {

		$cf_api = new Cloudflare_api($this->cloudflare_email, $this->cloudflare_key);
		$zone_data = $cf_api->get_zone_data();
		$zone_names =[];

	    foreach ($zone_data['result'] as $zone_name) {
	        array_push($zone_names, $zone_name['zone_name']);
	    }

	    return $zone_names;

	}
	/** 
	 * Check if there are multiple zone names. 
	 * If there are multiple show them to the user to let him decide 
	 * which one we'll be using for the calls to purge the cache.
	 * We'll store the domain he chooses in the DB.
	 * If there is only one that will be stored in the DB.
	*/
	public function multiple_zone_names() {

		$zone_names = $this->get_zone_names();
		if(count($zone_names) >1) {
			return 1;
		}elseif(count($zone_names) == 1) {
			return 0;
		}
		
	}
	/** 
	 * Get the domain that has been stored in the DB 
	*/
	public function get_stored_domain() {

		// Query CloudFlare database for domain
		$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '".$this->site_id."'");
		if ($query->num_rows > 0) {
			return $this->cloudflare_domain = $query->row('cloudflare_domain');
		}

	}
	/** 
	 * Get the zone_id based on the domain that is stored in the DB to use in purge functions
	*/
	public function get_zone_id() {

		$cf_api = new Cloudflare_api($this->cloudflare_email, $this->cloudflare_key);
		$zone_data = $cf_api->get_zone_data();
		$domain = $this->get_stored_domain();

		foreach ($zone_data['result'] as $val) {
			$zone_name = $val['zone_name'];
			$zone_id = $val['zone_id'];
			if($zone_name == $domain){
				return $zone_id;
			}
		}
		
	}
	/** 
	 * When the user submits urls to be purged they are stored in the DB. 
	 * This function retrieves the stored urls to be purged.
	*/
 	 public function get_stored_purge_urls() {

		// Query CloudFlare database for domain
		$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '".$this->site_id."'");
		if ($query->num_rows > 0) {
			return $this->purge_urls = $query->row('purge_urls');
		}

	}
	/** 
	 * Convert the stored urls to be purged into an array
	*/
	public function purge_urls_array($urls) {

		//explode string on both spaces and new lines
		$pattern = '/[\s\n]/';
		//check if string exists
		$input = isset($urls)?$urls:'';
		//explode and remove empty strings 
		$urls =  preg_split($pattern, $input, -1, PREG_SPLIT_NO_EMPTY);
		//var_dump($urls);
		return $urls;

	}
	/** 
	 * To Do: make sure the submitted urls start with http + stored domain
	*/

	/** 
	 * Purge everything. Uses the cloudflare api to purge all the cache.
	*/
	public function purge_everything() {

		$cf_api = new Cloudflare_api($this->cloudflare_email, $this->cloudflare_key);
		$response = $cf_api->purge_everything($this->get_zone_id());

		if ($response && $response['success'] == 'true') {
				return 'Cache has been purged.';
		} elseif(!$response) {
				return 'There was a problem connecting to CloudFlare. Please try again.';
		}

	}
	/** 
	 * Purge files. Uses the cloudflare api to purge individual urls.
	*/
	public function purge_files($urls) {

		$cf_api = new Cloudflare_api($this->cloudflare_email, $this->cloudflare_key);
		$response = $cf_api->purge_files($this->get_zone_id(), $urls);
	 
		if ($response && $response['success'] == 'true') {
				return  'The cache for the urls you submitted has been purged.';
		} elseif(!$response) {
				return 'There was a problem connecting to CloudFlare or the url you submitted was incorrect. Please try again.';
		}

	}

	// ========================================================
	// PRIVATE METHODS
	// ========================================================
 
	private function set_cloudflare_api() {

		// Query CloudFlare database for connection settings
		$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '".$this->site_id."'");
		if ($query->num_rows > 0) {
			
			$this->cloudflare_email		= $query->row('cloudflare_email');
			$this->cloudflare_key		= encrypt_decrypt_string('decrypt',$query->row('cloudflare_key') );
 
			$this->cf_api 				= new Cloudflare_api($this->cloudflare_email, $this->cloudflare_key);
 
			return 1; 
		} else {
			return 0; 
		}

	}

//end Cloudflare Class
}






<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cloudfare 
 *
 * @package			Cloudflare
 * @author			Michelle Kondou http://www.michellekondou.me
 * @copyright 		Copyright (c) 2016 Michelle Kondou
 */


class Cloudflare_mk_mcp {
 
 	var $stylesheet 		= "cloudflare.css";
 	var $url_themes 	= "/themes/third_party/cloudflare_mk";
 	var $base_url 			= "";
 
 	// DB parameters
 	var $cf					= null;


	function Cloudflare_mk_mcp()
	{
		$this->EE =& get_instance();

		require_once('libraries/cloudflare_mk.class.php');
		require_once('libraries/encrypt.php');
	 
		if (session_id() == "") 
		{
			session_start();
		}
	}

	// ============================================================
	// PAGES
	// ============================================================
	
	/**
	* DESC: Display Index Page
	*/
	function index()
	{	
		
		// --------------------------------------
		// INIT
		// --------------------------------------
		$vars 			= array();
		$site_id 		= $this->EE->config->item('site_id');	
		$group_id 		= $this->EE->session->userdata('group_id');
		$member_id 		= $this->EE->session->userdata('member_id');
 
		// --------------------------------------
		// 1. INCLUDE SHARED HEADER
		// --------------------------------------
		$this->_include_header('Cloudflare MK Dashboard');
 
		// --------------------------------------
		// 2. CREATE CLOUDFLARE OBJECT
		// --------------------------------------
		$this->cf = new Cloudflare_mk($site_id);
 
		// --------------------------------------
		// 3. SET PAGE DATA
		// --------------------------------------
		$vars['base_url'] 				= $this->base_url;
 		$vars['cloudflare_email']		= $this->cf->cloudflare_email;
		$vars['cloudflare_key']			= $this->cf->cloudflare_key;
		$vars['get_cloudflare_domains']	= $this->cf->get_zone_names();
		$vars['cloudflare_domain']		= $this->cf->get_stored_domain();
		$vars['purge_urls']				= $this->cf->get_stored_purge_urls();
		$vars['purge_urls_array']		= $this->cf->purge_urls_array($vars['purge_urls']); 
		$vars['zone_id']				= $this->cf->get_zone_id();

		$vars['message'] = $this->EE->session->flashdata('message')?$this->EE->session->flashdata('message'):'';

		// cloudflare has not been setup yet
		if(!$this->cf->has_settings) {
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk'.AMP.'method=settings');
			die();
		}

		// if there isn't a domain already stored 
		$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '$site_id'  AND cloudflare_domain = ''");
		if ($query->num_rows > 0) {	
			//if there are multiple domains associated with this account redirect to domains page to set the selected domain
			if ($this->cf->multiple_zone_names()) {
				$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk'.AMP.'method=domains');
				die();
			//if there is only one domain, store that domain in the DB
			}else{
				$this->set_domain();
				$vars['cloudflare_domain']	= $this->cf->get_stored_domain(); 
				$vars['zone_id'] 			= $this->cf->get_zone_id(); 
			}
		}
  
		return $this->EE->load->view('index', $vars, TRUE);
	}
	
	/**
	* DESC: Display Settings Page
	*/
	public function settings()
	{
 
		// --------------------------------------
		// INIT
		// --------------------------------------
		$vars 		= array();
		$site_id 	= $this->EE->config->item('site_id');
 
		// --------------------------------------
		// 1. INCLUDE SHARED HEADER
		// --------------------------------------
		$this->_include_header('Cloudflare MK Settings');
		
		// --------------------------------------
		// 2. CREATE CLOUDFLARE OBJECT
		// --------------------------------------
		$this->cf 	=  new Cloudflare_mk($site_id);
		
		// --------------------------------------
		// 3. SET PAGE DATA
		// --------------------------------------
		$vars['base_url'] 			= $this->base_url;
 		$vars['cloudflare_email']	= $this->cf->cloudflare_email;
		$vars['cloudflare_key']		= $this->cf->cloudflare_key;
		
		$vars['message'] = $this->EE->session->flashdata('message')?$this->EE->session->flashdata('message'):'';
 
		return $this->EE->load->view('settings', $vars, TRUE);

	}

	/**
	* DESC: Display Domains Page
	*/
	public function domains()
	{
 
		// --------------------------------------
		// INIT
		// --------------------------------------
		$vars 		= array();
		$site_id 	= $this->EE->config->item('site_id');
 
		// --------------------------------------
		// 1. INCLUDE SHARED HEADER
		// --------------------------------------
		$this->_include_header('Cloudflare MK Domains');
		
		// --------------------------------------
		// 2. CREATE CLOUDFLARE OBJECT
		// --------------------------------------
		$this->cf 	=  new Cloudflare_mk($site_id);
 
		
		// --------------------------------------
		// 3. SET PAGE DATA
		// --------------------------------------
		$vars['base_url'] 				= $this->base_url;
		$vars['get_cloudflare_domains']	= $this->cf->get_zone_names();
		$vars['cloudflare_domain']		= $this->cf->get_stored_domain();
		
		$vars['message'] = $this->EE->session->flashdata('message')?$this->EE->session->flashdata('message'):'';

		// if there is a domain stored
		$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '$site_id'  AND cloudflare_domain = ''");
		if ($query->num_rows == 0) {	
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk'.AMP.'method=index');
			die();
		}
 
		return $this->EE->load->view('domains', $vars, TRUE);

	}
	

	// ============================================================
	// PUBLIC METHODS
	// ============================================================

	public function update_settings()
	{
 
		// --------------------------------------
		// GET POST DATA
		// --------------------------------------
		$site_id 					= $this->EE->config->item('site_id');
		$errors						= 0;
		$vars['cloudflare_email']	= $this->EE->input->get_post('cloudflare_email');
		$vars['cloudflare_key']		= $this->EE->input->get_post('cloudflare_key');
		
		// --------------------------------------
		// VALIDATE
		// --------------------------------------
		// fields required
		if($vars['cloudflare_email'] == '' || $vars['cloudflare_key'] == ''){
			$vars['message']		= 'All fields required.';
			$errors 				= 1;
		}

		// VALIDATE CLOUDFLARE CREDENTIALS
		$this->cf 	=  new Cloudflare_mk($site_id,1);
		if( !$this->cf->valid_credentials($vars['cloudflare_email'],$vars['cloudflare_key'])) {
			$vars['message'] 	= "There was an error connecting to CloudFlare. Please make sure that your API key and email are correct.";
			$errors 			= 1;
		}
		
		// --------------------------------------
		// ENCRYPT ACCESS INFO
		// --------------------------------------
		$encrypted_api_key = encrypt_decrypt_string('encrypt', $vars['cloudflare_key']);
 
 
		// --------------------------------------
		// UPDATE OR INSERT
		// --------------------------------------
		if(!$errors){
			
			$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '$site_id'");
			
			if ($query->num_rows > 0) {			
				$record = $this->EE->db->query("
					UPDATE exp_cloudflare_mk 
					SET 
						cloudflare_email = '" . $vars['cloudflare_email'] . "', 
						cloudflare_key = '" . $encrypted_api_key . "',
						cloudflare_domain = '',
						purge_urls = ''
					WHERE 
						site_id = '" . $site_id . "'
				");
			} else {
				$record = $this->EE->db->query("
					INSERT INTO exp_cloudflare_mk 
						(site_id, cloudflare_email, cloudflare_key) 
					VALUES 
					('" . $site_id . "', '" . $vars['cloudflare_email'] . "', '" . $encrypted_api_key . "')
				");
			}
			
			if ($record) {
				$vars['message']		= 'Your Settings have been saved.';
			} else {
				$vars['message']		= 'There was a problem saving your Settings. Please try again!';				
			}
			
			// re-direct
			$this->EE->session->set_flashdata('message', $vars['message']);
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk' . AMP.'method=index');
	
		} else {
			
			$this->_include_header('Cloudflare Dashboard');
			
			$vars['base_url']	= $this->base_url;
			
			$vars['message']	= $this->EE->session->flashdata('message')?$this->EE->session->flashdata('message'):$vars['message'];
 
			return $this->EE->load->view('settings', $vars, TRUE);
			
		}
		
	}

	/**
	* DESC: If there is only one domain associated with this account
	* store that domain in the DB
	*/
	public function set_domain() {

		$vars['get_cloudflare_domains']	= $this->cf->get_zone_names();

		if (!$this->cf->multiple_zone_names()) {
			$this->EE->db->query("
				UPDATE exp_cloudflare_mk 
				SET 
					cloudflare_domain = '" . implode('',$vars['get_cloudflare_domains']) . "'
				WHERE 
					account_id = '1'
			");
		}

	}
	/**
	* DESC: If there are more than one domains associated with this account
	* let the user choose which domain to store in the DB
	*/
	public function update_domain() {
 
		// --------------------------------------
		// GET POST DATA
		// --------------------------------------
		$site_id 					= $this->EE->config->item('site_id');
		$errors						= 0;
		$vars['cloudflare_domain']	= $this->EE->input->get_post('cloudflare_domain');
		
		// --------------------------------------
		// VALIDATE
		// --------------------------------------
		// fields required
		if($vars['cloudflare_domain'] == ''){
			$vars['message']		= 'You must choose a domain.';
			$errors 				= 1;
		}
 
		// --------------------------------------
		// UPDATE 
		// --------------------------------------
		if(!$errors){
			
			$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '$site_id'");
			
			if ($query->num_rows == 1) {			
				$record = $this->EE->db->query("
					UPDATE exp_cloudflare_mk 
					SET 
						cloudflare_domain = '" . $vars['cloudflare_domain'] . "'
					WHERE 
						site_id = '" . $site_id . "'
				");
			} 

			if ($record) {
				$vars['message'] = 'Your domain has been saved.';
			}
			
			// re-direct
			$this->EE->session->set_flashdata('message', $vars['message']);
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk' . AMP.'method=index');
	
		} else {
			
			$this->_include_header('Cloudflare Dashboard');
			
			$vars['base_url']	= $this->base_url;
			
			$vars['message']	= $this->EE->session->flashdata('message')?$this->EE->session->flashdata('message'):$vars['message'];
 
			return $this->EE->load->view('settings', $vars, TRUE);
			
		}
		
	}
	/**
	* DESC: Store urls to be purged in the DB and purge the cache for them
	*  
	*/
 	public function update_purge_urls() {
 
		// --------------------------------------
		// GET POST DATA
		// --------------------------------------
		$site_id 			= $this->EE->config->item('site_id');
		$errors 			= 0;
		$vars['purge_urls'] = $this->EE->input->get_post('purge_urls');
		// --------------------------------------
		// 2. CREATE CLOUDFLARE OBJECT
		// --------------------------------------
		$this->cf = new Cloudflare_mk($site_id);
		$vars['get_cloudflare_domains']	= $this->cf->get_zone_names();
		$vars['cloudflare_domain']		= $this->cf->get_stored_domain();  
		$vars['purge_urls_array']		= $this->cf->purge_urls_array($vars['purge_urls']); 

		// --------------------------------------
		// VALIDATE
		// --------------------------------------
		// fields required
		if($vars['purge_urls'] == ''){
			$vars['message']		= 'You must submit a url.';
			$errors 				= 1;
		}
 
		// --------------------------------------
		// UPDATE 
		// --------------------------------------
		if(!$errors){
			
			$query = $this->EE->db->query("SELECT * FROM exp_cloudflare_mk WHERE site_id = '$site_id'");
			
			if ($query->num_rows == 1) {			
				$record = $this->EE->db->query("
					UPDATE exp_cloudflare_mk 
					SET 
						purge_urls = '" . $vars['purge_urls'] . "'
					WHERE 
						site_id = '" . $site_id . "'
				");
			} 
						
			//purge urls 
			$vars['message'] = $this->cf->purge_files($this->cf->purge_urls_array($vars['purge_urls']));
			// re-direct
			$this->EE->session->set_flashdata('message', $vars['message']);
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk' . AMP.'method=index');
	
		} else {
			
			$this->_include_header('Cloudflare Dashboard');
			
			$vars['base_url']	= $this->base_url;
			
			$vars['message']	= $this->EE->session->flashdata('message')?$this->EE->session->flashdata('message'):$vars['message'];
 			
			return $this->EE->load->view('index', $vars, TRUE);
			
		}
		
	}

	public function purge_everything(){
		
		$site_id = $this->EE->config->item('site_id');
		
		$this->cf = new Cloudflare_mk($site_id);
		$vars['message'] = $this->cf->purge_everything();
		 
		$this->EE->session->set_flashdata('message', $vars['message']);			
		$this->EE->functions->redirect(BASE . AMP . 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk');

	}
 
	// ========================================================
	// PRIVATE METHODS
	// ========================================================
	
	private function _include_header($title){
		
		// Load helpers
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		// include assets
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->url_themes . '/css/bootstrap.min.css" />');
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->url_themes . '/css/bootstrap-theme.min.css" />');
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->url_themes . '/css/bootstrap-toggle.min.css" />');
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $this->url_themes . '/css/' . $this->stylesheet . '" />');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->url_themes . '/js/bootstrap.min.js"></script>');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->url_themes . '/js/bootstrap-toggle.min.js"></script>');
		// Add javascript
		$this->EE->javascript->compile();
		
		
		$this->EE->view->cp_page_title = $title;
		$this->base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cloudflare_mk';
		 
		
	}

}
// END CLASS

/* End of file mcp.cloudflare_mk.php */
/* Location: ./system/expressionengine/third_party/modules/cloudflare_mk/mcp.cloudflare_mk.php */

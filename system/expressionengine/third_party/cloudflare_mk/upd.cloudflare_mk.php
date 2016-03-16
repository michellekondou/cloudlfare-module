<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cloudfare  
 *
 * @package			Cloudflare
 * @author			Michelle Kondou http://www.michellekondou.me
 * @copyright 		Copyright (c) 2016 Michelle Kondou
 */

// include config file
include_once dirname(dirname(__FILE__)).'/cloudflare_mk/config.php';

class Cloudflare_mk_upd {

	public $version				= MODULE_VERSION;
	private $module_name		= MODULE_CLASS_NAME;
	private $has_cp_backend 	= 'y';
	private $has_publish_fields = 'n';
	
	function Cloudflare_mk_upd()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */	
	function install()
	{
		$this->EE->load->dbforge();

		//----------------------------------------
		// EXP_MODULES
		//----------------------------------------
		$module = array(	
			'module_name' => ucfirst($this->module_name),
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n' 
		);
							
		$this->EE->db->insert('modules', $module);

		//----------------------------------------
		// EXP_CLOUDFLARE
		//----------------------------------------
		$ci = array(
			'account_id'		=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'site_id'			=> array('type' => 'TINYINT',	'unsigned' => TRUE,	'default' => 1),
			'cloudflare_email'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'cloudflare_key'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'cloudflare_domain'	=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'purge_urls'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => '')
		);

		$this->EE->dbforge->add_field($ci);
		$this->EE->dbforge->add_key('account_id', TRUE);
		$this->EE->dbforge->add_key('site_id');
		$this->EE->dbforge->create_table('cloudflare_mk', TRUE);

		return TRUE; 
	}
	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */
	function uninstall()
	{
		// Load dbforge
		$this->EE->load->dbforge();
 
		// Remove
		$this->EE->dbforge->drop_table('cloudflare_mk');
 
		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->delete('modules');
		$this->EE->db->where('class', ucfirst($this->module_name));
		$this->EE->db->delete('actions');

		return TRUE;
	}



	// --------------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @access	public
	 * @return	bool
	 */	
	
	function update($current='')
	{
		// Are they the same?
		if (version_compare($current, $this->version) >= 0)
		{
			return FALSE;
		}

		// Two Digits? (needs to be 3)
		if (strlen($current) == 2) $current .= '0';

		$update_dir = PATH_THIRD.strtolower($this->module_name).'/updates/';

		// Does our folder exist?
		if (@is_dir($update_dir) === TRUE)
		{
			// Loop over all files
			$files = @scandir($update_dir);

			if (is_array($files) == TRUE)
			{
				foreach ($files as $file)
				{
					if (strpos($file, '.php') === false) continue;
					if (strpos($file, '_') === false) continue; // For legacy: XXX.php
					if ($file == '.' OR $file == '..' OR strtolower($file) == '.ds_store') continue;

					// Get the version number
					$ver = substr($file, 0, -4);
					$ver = str_replace('_', '.', $ver);

					// We only want greater ones
					if (version_compare($current, $ver)  >= 0) continue;

					require $update_dir . $file;
					$class = 'CloudflareUpdate_' . str_replace('.', '', $ver);
					$UPD = new $class();
					$UPD->do_update();
				}
			}
		}

		// Upgrade The Module
		$this->EE->db->set('module_version', $this->version);
		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->update('exp_modules');

		return TRUE;
	}
	
}
/* END Class */

/* End of file upd.cloudflare_mk.php */
/* Location: ./system/expressionengine/third_party/modules/cloudflare_mk/upd.cloudflare_mk.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
/**
 * Cloudfare  
 *
 * @package			Cloudflare
 * @author			Michelle Kondou http://www.michellekondou.me
 * @copyright 		Copyright (c) 2016 Michelle Kondou
 */
class Cloudflare_mk extends Channel {

	/**
     * Constructor function
     */
    function __construct() {
         
        // run standard Channel module constructor
        parent::Channel();
    }
	
	function Cloudflare()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

	}
		
}

/* End of file mod.cloudflare_mk.php */
/* Location: ./system/expressionengine/third_party/modules/cloudflare_mk/mod.cloudflare_mk.php */
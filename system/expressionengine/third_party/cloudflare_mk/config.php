<?php

/**
 * Config file for Cloudflare
 *
 * @package			Cloudflare
 * @author			Michelle Kondou http://www.michellekondou.me
 * @copyright 		Copyright (c) 2016 Michelle Kondou
 */

if ( ! defined('MODULE_NAME'))
{
	define('MODULE_NAME',         'CloudFlare MK');
	define('MODULE_CLASS_NAME',   'cloudflare_mk');
	define('MODULE_VERSION',      '1.0.0');
}

$config['name'] 	= MODULE_NAME;
$config["version"] 	= MODULE_VERSION;

//----------------------------------------
// < EE 2.6.0 backward compat
//----------------------------------------
if (!function_exists('ee'))
{
    function ee()
    {
        static $EE;
        if ( ! $EE) $EE = get_instance();
        return $EE;
    }
}

 

/* End of file config.php */
/* Location: ./system/expressionengine/third_party/cloudflare_mk/config.php */
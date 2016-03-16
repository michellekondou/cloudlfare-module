<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cloudfare MK
 *
 * @package     Cloudflare MK
 * @author      Michelle Kondou http://www.michellekondou.me
 * @copyright   Copyright (c) 2016 Michelle Kondou
 */

class Cloudflare_api {

/**
 * CloudFlare API base endpoint.
 *
 * @since 1.0
 * @access protected
 *
 * @var string
 */
protected $base_endpoint = 'https://api.cloudflare.com/client/v4/';

/**
 * CloudFlare API key.
 *
 * @since 1.0
 * @access protected
 *
 * @var string
 */
protected $api_key;

/**
 * CloudFlare email.
 *
 * @since 1.0
 * @access protected
 *
 * @var string
 */
protected $email;

/**
 * CloudFlare headers.
 *
 * @since 1.0
 * @access protected
 *
 */

public function __construct($email, $api_key)
{
  // Set CloudFlare API values
  $this->email = $email;
  $this->api_key = $api_key;
}

private function get_headers() {

  $headers = [
    'X-Auth-Email:'.$this->email,
    'X-Auth-Key:'.$this->api_key,
    'Content-Type:application/json',
  ];
  return $headers;

}

public function get_zone_data() {

  $data = array(
    'callback'  => 'user/billing/subscriptions/zones',
    'method'    => 'GET'
  ); 
  
  return $this->http_post($data['callback'], $data['method']);

}

public function purge_everything($zone_id) {

  $data = array(
      'callback'  => 'zones/'.$zone_id.'/purge_cache',
      'method'    => 'DELETE',
      'body'      => ['purge_everything' => true]
  ); 

  return $this->http_post($data['callback'], $data['method'], $data['body']);

}

public function purge_files($zone_id, $urls) {
 
  $data = array(
      'callback'  => 'zones/'.$zone_id.'/purge_cache',
      'method'    => 'DELETE',
      'body'      =>  
        array(
          'files' => $urls,
        )
      ,
    );
  // var_dump($data);
  return $this->http_post($data['callback'], $data['method'], $data['body']);

}

private function http_post($callback, $method, $body = false) {

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $this->base_endpoint.$callback);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $this->get_headers());
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //execute the curl query
  $result = curl_exec($ch);
  $error = curl_error($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //shut down the curl system
  curl_close($ch);

  if ($http_code != 200) {
    return array();
  }else{
    //echo $result;
    return json_decode($result, true);
  }

}

//end cloudflare_api class
}


?>



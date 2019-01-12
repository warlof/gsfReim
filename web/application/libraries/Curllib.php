<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Curllib
{
    public function makeRequest($type, $url, $postdata = '', $headers = array()){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'GSF Affordable Care');

		if($type == "POST"){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}

		if(count($headers) > 0){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$res = curl_exec($ch);

		$tracking = curl_getinfo($ch);

		log_message('debug', sprintf('%s[%d] %s', $type, $tracking['http_code'], $url));

		if ($res === false)
		    log_message('debug', sprintf('An error occured while processing the request %s %s %s - %s', $type, curl_errno($ch), $url, curl_error($ch)));

		curl_close($ch);

		return $res;
	}
}

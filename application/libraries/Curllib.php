<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Curllib
{
    public function makeRequest($type, $url, $postdata = '', $headers = array()){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'GSF Affordable Care');

		if($type == "POST"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}

		if(count($headers) > 0){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$res = curl_exec($ch);
		curl_close($ch);

		return $res;
	}
}

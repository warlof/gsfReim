<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	function getInsurance(){
		$data = $this->curllib->makeRequest('GET', 'https://esi.tech.ccp.is/latest/insurance/prices/?datasource=tranquility&language=en-us');
		$dataArray = json_decode($data, TRUE);
		if(count($dataArray) > 0){
			//We have data to work with, process it. First thing we're going to do is nuke the old data, this is unimportant if it gets fucked and lost.
			$this->db->truncate("shipInsurance");

			foreach($dataArray as $type){
				$typeID = $type['type_id'];
				$levels = $type['levels'];
				$dti = array(
					    "typeID"	=>	$typeID,
					);
				foreach($levels as $level){
					switch($level['name']){
						case "Basic":
							$dti["basicCost"] = $level['cost'];
							$dti['basicPayout'] = $level['payout'];
							break;
						case "Standard":
							$dti["standardCost"] = $level['cost'];
							$dti['standardPayout'] = $level['payout'];
							break;
						case "Bronze":
							$dti["bronzeCost"] = $level['cost'];
							$dti['bronzePayout'] = $level['payout'];
							break;
						case "Silver":
							$dti["silverCost"] = $level['cost'];
							$dti['silverPayout'] = $level['payout'];
							break;
						case "Gold":
							$dti["goldCost"] = $level['cost'];
							$dti['goldPayout'] = $level['payout'];
							break;
						case "Platinum":
							$dti["platinumCost"] = $level['cost'];
							$dti['platinumPayout'] = $level['payout'];
							break;
						default:
							break;
					}
				}
				$this->db->insert("shipInsurance", $dti);
			}
		}
	}

	function test() {
		print "<pre>";
		print_r($this->session->all_userdata());
		print "</pre>";
	}
}
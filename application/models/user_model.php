<?php
class User_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->crowdUrl = "https://crowd.goonfleet.com/crowd/rest/usermanagement/1/";
		$this->crowdUser = "reimb-kilgarth";
		$this->crowdPass = "wormdegreestillbase";
	}
	function get_val($str, $key1, $key2) {
		if(strpos('@@@' . $str, $key1) <> 0 && strpos('@@@' . $str, $key2) <> 0) {
			$MyVal = explode($key1, $str);
			$MyVal2 = explode($key2, $MyVal[1]);
			return $MyVal2[0];
		} else {
			return '';
		}
	}
	
	function check_login($user,$password){
		$auth_method = $this->config->item("AUTH_METHOD");
		$return = array();
		$isReim = 0;
		$isReimDir = 0;
		$inCapSwarm = 0;
		if($auth_method == "CROWD"){
			$this->proxy->set_http(array('head' => array('Authorization' => "Basic ".base64_encode($this->crowdUser.":".$this->crowdPass), 'Content-Type' => 'application/xml', 'Accept' => 'application/xml')));
			$xmlBody = '<?xml version="1.0" encoding="UTF-8"?>
								<password>
	  								<value>@password@</value>
								</password>';
			$requestBody = str_replace('@password@',$password, $xmlBody);
			$crowdData = $this->proxy->http('POST', $this->crowdUrl."authentication?username=" . urlencode($user), array('' => $requestBody));
			
			if(strpos($crowdData, '<error>') > 0){
				$return['err'] = "BAD USER";
				$return['errReason'] = $this->get_val($crowdData, '<reason>','</reason>');
				$return['errMessage'] = $this->get_val($crowdData, '<message>','</message>');
			} else {
				$groupData = $this->getGroups($user);
				if(strpos($groupData, '[SIG] Incompetence Compensators') !== FALSE){
					$isReim = 1;
				}
				if(strpos($groupData, '[A] Directors of Reimbursement') !== FALSE){
					$isReimDir = 1;
				}
				if(strpos($groupData, '[SIG] CapSwarm') !== FALSE){
					$inCapSwarm = 1;
				}
				if($user == 'kilgarth' || $user == 'innominate'){
					$isReim = 1;
					$isReimDir = 1;
				}
				$return['isReim'] = $isReim;
				$return['isReimDir'] = $isReimDir;
				$return['inCapSwarm'] = $inCapSwarm;
				$return['err'] = FALSE;
			}
		} elseif($auth_method == "INTERNAL"){
			$groups = array();
			$uchk = $this->db->select('user, id, gids')->where('user', $user)->where('password', sha1($password))->where('active', 1)->get('users');
			if($uchk->num_rows() > 0){
				$uid = $uchk->row(0)->id;
				if(strlen($uchk->row(0)->gids) > 0){
					$gids = explode(",",$uchk->row(0)->gids);
					if(in_array(1, $gids)){
						$isReim = 1;
					}
					if(in_array(2, $gids)){
						$isReimDir = 1;
					}
					if(in_array(3, $gids)){
						$inCapSwarm = 1;
					}
				}
				$return['isReim'] = $isReim;
				$return['isReimDir'] = $isReimDir;
				$return['inCapSwarm'] = $inCapSwarm;
				$return['err'] = FALSE;
			} else {
				$return['err'] = "BAD USER";
				$return['errReason'] = "Invalid username/password.";
				$return['errMessage'] = "Invalid username/password.";
			}
		} else {
			$return['err'] = "BAD AUTH METHOD";
			$return['errReason'] = "NO AUTH METHOD SET";
			$return['errMessage'] = "NO AUTH METHOD SET";
		}
			
		return $return;
	}
	
	function getGroups($user){
		$this->proxy->set_http(array('head' => array('Authorization' => "Basic ".base64_encode($this->crowdUser.":".$this->crowdPass), 'Content-Type' => 'application/xml', 'Accept' => 'application/xml')));
		$groupData = $this->proxy->http('GET', $this->crowdUrl."user/group/direct?username=".urlencode($user));
		return $groupData;
	}
	
	function registerUser($user, $password) {
		$auth_method = $this->config->item("AUTH_METHOD");
		if($auth_method == "INTERNAL"){
			$uchk = $this->db->where('user', $user)->get('users');
			if($uchk->num_rows() > 0){
				$return['err'] = "User Exists. Please refresh the page and try again, or wait 3 seconds for the page to automatically refresh.";
			} else {
				$this->db->insert("users", array('user' => $user, "password" => sha1($password)));
				$return['err'] = FALSE;
				$return['message'] = "User Created.";
			}
			return $return;
		} else {
			show_error("Invalid request. Auth method is not set to internal, cannot register user.", 500);
		}
	}
}

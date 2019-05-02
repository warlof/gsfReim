<?php
class User_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->crowdUrl = $this->config->item("CROWD_URL");;
		$this->crowdUser = $this->config->item("CROWD_USERNAME");
		$this->crowdPass = $this->config->item("CROWD_PASSWORD");
		$this->managerToken = $this->config->item("MANAGER_TOKEN");
		$this->managerBase = $this->config->item("MANAGER_BASE");
		$this->authGroups = $this->config->item("AUTH_GROUPS");
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

		switch ($auth_method) {
            case 'CROWD':
                return $this->crowdAuthentication($user, $password);
            case 'ESI':
                return $this->esiAuthentication($user, $password);
            case 'INTERNAL':
                return $this->internalAuthentication($user, $password);
        }

		return [
		    'err' => 'BAD AUTH METHOD',
            'errReason' => 'NO AUTH METHOD SET',
            'errMessage' => 'NO AUTH METHOD SET',
        ];
	}

	function getGroups($user){
		$headers = array("Authorization: Basic " . base64_encode($this->crowdUser.":".$this->crowdPass), 'Content-Type: application/xml', 'Accept: application/xml');
		$groupData = $this->curllib->makeRequest('GET', $this->crowdUrl."user/group/direct?username=".urlencode($user),'',$headers);

		$gXMLData = simplexml_load_string($groupData);
		$groups = array();
		foreach($gXMLData->group as $group){
			$groups[] = (string) $group->attributes()->name[0];
		}
		return $groups;
	}

	function getMemberID($user){
		$vrdb = $this->load->database("vilerat",TRUE);
		$sql = "SELECT member_id FROM gsfForums.members WHERE replace(name, '&#39;', '\'') = ?";
		$member_data = $vrdb->query($sql, array($user));
		if($member_data->num_rows() > 0){
			return $member_data->row(0)->member_id;
		} else {
			return 0;
		}
	}

	function getLoginStats($name){
		$memberID = $this->getMemberID($name);
		if($memberID == 0){
			log_message("error", "Unable to get member ID for $name.");
			return array();
		}
		$headers = array("Authorization: Bearer ".$this->managerToken);
		$data = $this->curllib->makeRequest("GET", sprintf("%s/account_detail/%s", $this->managerBase, $memberID),"", $headers);
		$decoded = json_decode($data, TRUE);

		if(count($decoded)){
			return $decoded[0];
		} else {
			return array();
		}
	}

	function registerUser($user, $password) {
		$auth_method = $this->config->item("AUTH_METHOD");

		if (in_array($auth_method, ['INTERNAL', 'ESI'])) {
			$uchk = $this->db->where('user', $user)->get('users');

			if($uchk->num_rows() > 0)
				return [
                    'err' => "User Exists. Please refresh the page and try again, or wait 3 seconds for the page to automatically refresh.",
                ];

            $this->db->insert("users", array('user' => $user, "password" => sha1($password)));

            return [
                'err' => false,
                'message' => 'User Created.',
            ];
		} else {
			show_error("Invalid request. Auth method is not set to internal, cannot register user.", 500);
		}
	}

	private function crowdAuthentication($user, $password)
    {
        $return = [
            'err' => false,
        ];

        $getStats = $this->config->item("getStats");

        $headers = array("Authorization: Basic " . base64_encode($this->crowdUser.":".$this->crowdPass), 'Content-Type: application/xml', 'Accept: application/xml');
        $xmlBody = '<?xml version="1.0" encoding="UTF-8"?>
								<password>
	  								<value>@password@</value>
								</password>';
        $requestBody = str_replace('@password@',$password, $xmlBody);
        $crowdData = $this->curllib->makeRequest('POST', $this->crowdUrl."authentication?username=" . urlencode($user), $requestBody, $headers);

        if(strpos($crowdData, '<error>') > 0){
            $return['err'] = "BAD USER";
            $return['errReason'] = $this->get_val($crowdData, '<reason>','</reason>');
            $return['errMessage'] = $this->get_val($crowdData, '<message>','</message>');
        } else {
            $groupData = $this->getGroups($user);
            $return['groupData'] = $groupData;
            if(count($groupData) <= 0){
                $d = json_encode($groupData);
                log_message("error", "Something went wrong getting groups for $user. The data returned was: $d");

                $return['err'] = "LOGINERR";
                $return['errReason'] = 'Something happened on login getting groups';
                $return['errMessage'] = 'Something happened on login getting groups';
            } else {
                //First thing we need to do before moving forward is check to see if this user is in a banned group.
                $groupBans = $this->db->select("groupName, banDate, bannedBy, reason")->where_in("groupName", $groupData)->where("active", '1')->get("bannedGroups");

                if($groupBans->num_rows() > 0){
                    $gName = $groupBans->row(0)->groupName;
                    $bannedBy = $groupBans->row(0)->bannedBy;
                    $banDate = $groupBans->row(0)->banDate;

                    $return['err'] = "GROUPBAN";
                    $return['errReason'] = $groupBans->row(0)->reason;
                    $return['errMessage'] = "Group ban issued to $gName by $bannedBy on $banDate.";

                }

                if(in_array($this->authGroups['REIMBURSEMENT'], $groupData))
                    $isReim = 1;

                if(in_array($this->authGroups['REIMDIRS'], $groupData))
                    $isReimDir = 1;

                if(in_array($this->authGroups['CAPSWARM'], $groupData))
                    $inCapSwarm = 1;

                if(in_array($this->authGroups['CAPSWARM_REIM'],$groupData)){
                    $isCapDir = 1;
                    $isReimDir = 1;
                }

                foreach($this->authGroups['ADMINS'] as $admins){
                    if(in_array($admins, $groupData)){
                        $isReim = 1;
                        $isReimDir = 1;
                        $isCapDir = 1;
                    }
                }

                $return['isReim'] = $isReim;
                $return['isReimDir'] = $isReimDir;
                $return['inCapSwarm'] = $inCapSwarm;
                $return['isCapDir'] = $isCapDir;

                if($getStats){
                    //We need to get login statistics
                    $loginStats = $this->getLoginStats($user);
                    $return = array_merge($return,$loginStats);
                }
            }
        }

        return $return;
    }

    private function esiAuthentication($character_name, $password)
    {
        $uchk = $this->db->select('user, id, gids, active')->where('user', $character_name)->get('users');

        if ($uchk->num_rows() < 1) {
            if (! $this->config->item('ALLOW_REGISTRATION')) {
                return [
                    'err' => 'BAD USER',
                    'errReason' => 'Unregistered character',
                    'errMessage' => 'Unregistered character',
                ];
            }

            $ret = $this->registerUser($character_name, $password);

            if (! $ret['message'])
                return [
                    'err' => $ret['err'],
                    'errReason' => 'Account creation failed',
                    'errMessage' => 'Account creation failed',
                ];

            $uchk = $this->db->select('user, id, gids, active')->where('user', $character_name)->get('users');
        }

        $user = $uchk->row(0);

        if ($user->active != 1)
            return [
                'err' => 'DISABLED',
                'errReason' => 'Your account has been disabled.',
                'errMessage' => 'Please contact SkyTeam in order to get extra information.',
            ];

        $gids = [];

        if(strlen($user->gids) > 0)
            $gids = explode(",", $user->gids);

        return [
            'err' => false,
            'isReim' => (int) in_array(1, $gids),
            'isReimDir' => (int) in_array(2, $gids) || in_array(4, $gids),
            'inCapSwarm' => (int) in_array(3, $gids),
            'isCapDir' => (int) in_array(4, $gids),
        ];
    }

    private function internalAuthentication($user, $password)
    {
        $return = [
            'err' => false,
        ];

        $uchk = $this->db->select('user, id, gids')->where('user', $user)->where('password', sha1($password))->where('active', 1)->get('users');

        if($uchk->num_rows() > 0) {
            $gids = [];

            if(strlen($uchk->row(0)->gids) > 0)
                $gids = explode(",",$uchk->row(0)->gids);

            $return['isReim'] = (int) in_array(1, $gids);
            $return['isReimDir'] = (int) in_array(2, $gids) || in_array(4, $gids);
            $return['inCapSwarm'] = (int) in_array(3, $gids);
            $return['isCapDir'] = (int) in_array(4, $gids);
        } else {
            $return['err'] = "BAD USER";
            $return['errReason'] = "Invalid username/password.";
            $return['errMessage'] = "Invalid username/password.";
        }

        return $return;
    }
}

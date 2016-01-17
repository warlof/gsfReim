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
	function get_val($str, $key1, $key2) {
		if(strpos('@@@' . $str, $key1) <> 0 && strpos('@@@' . $str, $key2) <> 0) {
			$MyVal = explode($key1, $str);
			$MyVal2 = explode($key2, $MyVal[1]);
			return $MyVal2[0];
		} else {
			return '';
		}
	}
	
	function getwalletdata() {
		$this->proxy->set_http(array('head' => array('Authorization' => "Basic ".base64_encode("kilgarth:voicepowercooksheep"))));

		return $this->proxy->http('GET', 'https://apitool.goonfleet.com/corp/corp/WalletJournal.xml.aspx?rowCount=2560&keyid=667531913&vcode=ok&accountKey=1001');
	}
	
	function reimWallet(){
		$apidata = $this->getwalletdata();
		if(!strpos($apidata, '<error code="') > 0) {
			if(strpos($apidata, '</eveapi>') > 0) {
				$apirows = explode('<row da', $apidata);
				$i = 0;
				foreach ($apirows as $row) {
					if(strpos($row, '" refID="') != 0) {
						$refid = $this->get_val($row, 'refID="' , '" ');
						$yyyymm = substr($this->get_val($row, 'te="' , '" '), 0, 7);
						$reftype = $this->get_val($row, 'refTypeID="' , '" ');
						$recownerid = $this->get_val($row, 'ownerID2="' , '" ');
						$reason = $this->get_val($row, 'reason="' , '" ');
						if(substr($reason, 0, 6) == 'DESC: '){
							$reason = substr($reason, 0, -5);
							$reason = substr($reason, 6);
						}
						$recdata = array(	'refid'			=> $this->get_val($row, 'refID="' , '" '),
											'corpid'		=> 667531913,
											'wid'			=> '1001',
											'reftypeid'		=> $this->get_val($row, 'refTypeID="' , '" '),
											'ownername1'	=> $this->get_val($row, 'ownerName1="' , '" '),
											'ownerid1'		=> $this->get_val($row, 'ownerID1="' , '" '),
											'ownername2'	=> $this->get_val($row, 'ownerName2="' , '" '),
											'ownerid2'		=> $this->get_val($row, 'ownerID2="' , '" '),
											'argname1'		=> $this->get_val($row, 'argName1="' , '" '),
											'argid1'		=> $this->get_val($row, 'argID1="' , '" '),
											'amount'		=> $this->get_val($row, 'amount="' , '" '),
											'balance'		=> $this->get_val($row, 'balance="' , '" '),
											'reason'		=> $reason,
											'yyyy-mm'		=> $yyyymm,
											'tdate' 		=> $this->get_val($row, 'te="' , '" '));
						$this->db->where('refid', $refid);
						$recquery = $this->db->get('walletJournal');
						if($recquery->num_rows() == 0 ) {
							$this->db->insert('walletJournal', $recdata);
							$i++;
						}
					}
				}
				echo $i . " Rows Added.";
			}
		}
	}
}
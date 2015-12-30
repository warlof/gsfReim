<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

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
	function __construct(){
		parent::__construct();
		$this->logged_in = FALSE;
		$this->isReim = FALSE;
		$this->isReimDir = FALSE;
		$this->isBanned = FALSE;
		$this->vars = $this->session->userdata('vars');
	 	if($this->vars['logged_in'] == TRUE){
	 		$this->logged_in = TRUE;
			if($this->vars['isReim'] == 1){
		 		$this->isReim = TRUE;
		 	}
			if($this->vars['isReimDir'] == 1){
		 		$this->isReimDir = TRUE;
		 	}
			if($this->vars['isBanned'] == 1){
				$this->isBanned = TRUE;
			}
			$acceptLosses = $this->db->where('name','acceptLosses')->get('adminSettings');
			$this->accLosses = $acceptLosses->row(0)->value;
	 	}
	}

	function createAdmin(){
		if($this->config->item('AUTH_METHOD') == "INTERNAL"){
			$dbchk = $this->db->where('user', 'admin')->get('users');
			if($dbchk->num_rows() == 0){
				$dti = array("user" => "admin", "password" => sha1($this->config->item('ADMIN_PASSWORD')), 'gids' => '1,2,3', 'id' => 1);
				$this->db->insert('users', $dti);
				echo "Admin user created.";
			} else {
				echo "The admin account already exists.";
			}
		} else {
			echo "This site is not configured to use internal auth.";
		}
	}

	public function index() {
		$data['kills'] = $this->db->where('paid', 0)->where('reservedBy', $this->session->userdata('vars')['user'])->order_by('timestamp', 'ASC')->get('kills');
		$data['allKills'] = $this->db->where('paid',0)->where('reservedBy', NULL)->get('kills');
		$acceptLosses = $this->db->where('name','acceptLosses')->get('adminSettings');
		$data['acceptLosses'] = $acceptLosses->row(0)->value;
		
		$this->load->view('header');
		$this->load->view('home', $data);
		$this->load->view('footer');
	}
	
	function submitKill(){
		$low = array('11','12','13','14','15','16','17','18');
		$med = array('19','20','21','22','23','24','25','26');
		$high = array('27','28','29','30','31','32','33','34');
		$rigs = array('92','93','94','95','96','97','98','99');
		$fit = array();
		$user = $this->vars['user'];
		$carriers = array('23757','23911','23915','24483');
		
		if($this->accLosses == 1){
			if($this->logged_in && !$this->isBanned){
				$crestLink = $this->input->post('crestLink', TRUE);
				$bcastText = $this->input->post('bcast', TRUE);
				if(strpos($crestLink, 'crest.eveonline') > 0){
					$crestData = json_decode($this->proxy->http('GET', $crestLink), TRUE);
					if(isset($crestData['exceptionType']) && $crestData['exceptionType'] == 'ForbiddenError'){
						echo "There was an error, please check your link and try again. If you continue to see this error, please contact an administrator.";
					} else {
		
						//Parse the body to extract the data we care about
						$killID = $crestData['killID'];
						$killTime = $crestData['killTime'];
						$numAttackers = $crestData['attackerCount'];
						$sysID = $crestData['solarSystem']['id'];
						$sysName = $crestData['solarSystem']['name'];
						$vicName = $crestData['victim']['character']['name'];
						$vicID = $crestData['victim']['character']['id'];
						$vicShipTypeID = $crestData['victim']['shipType']['id'];
						$vicShipTypeName = $crestData['victim']['shipType']['name'];
						$vicCorpID = $crestData['victim']['corporation']['id'];
						$vicCorpName = $crestData['victim']['corporation']['name'];
						$damageTaken = $crestData['victim']['damageTaken'];
						$attackers = $crestData["attackers"];
						$attackerArr = array();
						$i = 0;
						foreach($attackers as $key => $value){
							$alliance = '';
							if(isset($value['alliance'])){
								$alliance = $value['alliance']['name'];
							}
							if(isset($value['shipType'])){
								$shiptype = $value['shipType']['name'];
							} else {
								$shiptype = "Unknown";
							}
							if(isset($value['character'])){
									$character = $value['character']['name'];
							} else {
								$character = 'Unknown ' . $i;
							}
							if(isset($value['weaponType'])){
								$weaponType = $value['weaponType']['name'];
							} else {
								$weaponType = "Unknown";
							}
							if(isset($value['corporation'])){
								$corporation = $value['corporation']['name'];
							} else {
								$corporation = "Unknown Corporation";
							}
							$attackerArr[$character] = array('corporation' => $corporation,
																			'alliance' => $alliance,
																			'shipType' => $shiptype,
																			'weaponType' => $weaponType,
																			'damageDone' => $value['damageDone_str']);
							$i++;
						}
						
						$curDate = new DateTime("now");
						$killTime = str_replace('.','-',$killTime);
						$killDate = new DateTime($killTime); 
						$diff = $curDate->diff($killDate);
						$dayDiff = $diff->format('%a');
						$aDd = $this->db->where('name', 'maxDayDiff')->get('adminSettings');
						$allowedDateDiff = $aDd->row(0)->value;
						
						if($dayDiff <= $allowedDateDiff){
							$sysChk = $this->db->where('sys_eve_id', $sysID)->get('vwsysconreg');
							$regID = $sysChk->row(0)->reg_id;
							$regName = $sysChk->row(0)->reg_name;
							$secStatus = $sysChk->row(0)->sec;
							$ptQualified = 0;
							$overPtCap = 0;
							
							$ptChk = $this->db->where('regID', $regID)->get("ptRegions");
							if($ptChk->num_rows() > 0){
								$ptQualified = 1;
								$ptCap = $this->db->where('name', 'ptCap')->get('adminSettings');
								$ptCapVal = $ptCap->row(0)->value;
								$d = $killDate->format('Y-m');
								$ptAmtChk = $this->db->where('submittedBy', $user)->where('month', $d)->get('vwptcap');
								if($ptAmtChk->num_rows() > 0 && $ptAmtChk->row(0)->total >= $ptCapVal){
									$overPtCap = 1;
								}
							}
	
							//Lets loop through the items and add them to an array so that we can figure out where they were on the ship.
							foreach($crestData['victim']['items'] as $item){
								if(in_array($item['flag'], $high)){
									$fit['high'][] = $item['itemType']['name'];
								} elseif(in_array($item['flag'], $med)){
									$fit['med'][] = $item['itemType']['name'];
								} elseif(in_array($item['flag'], $low)){
									$fit['low'][] = $item['itemType']['name'];
								} elseif($item['flag'] == 5){
									$fit['cargo'][] = $item['itemType']['name'];
								} elseif(in_array($item['flag'], $rigs)){
									$fit['rigs'][] = $item['itemType']['name'];
								} else {
									$fit['other'][] = $item['itemType']['name'];
								}
							}
							//Check to see if the kill exists
							$dbchk = $this->db->where('killID', $killID)->get('kills');
							if($dbchk->num_rows() > 0){
								//It exists, so notify user as such
								echo "This kill has already been submitted.";
							} elseif ((in_array($vicShipTypeID,$carriers) && $this->vars['inCapSwarm'] == 1) || !in_array($vicShipTypeID,$carriers)) {
								//Kill does NOT exist in db, so lets see if there is a payout set for this ship
								$poChk = $this->db->select('p.payoutAmount, p.payoutType, p.typeID, p.id, p.typeName')->from('payouts p')->join('payoutTypes pt','pt.id = p.payoutType')->where('p.typeID', $vicShipTypeID)->where('pt.active', '1')->get();
								$payoutArr = array();
								if($poChk->num_rows() > 0){
									if($vicCorpID == 667531913){
										$bonusamt = $this->db->where('name', 'waffeBonus')->get('adminSettings');
										$bonusCap = $this->db->where('name', 'waffeBonusCap')->get('adminSettings');
										if($bonusamt->num_rows() > 0){
											$bonus = $bonusamt->row(0)->value;
										} else {
											$bonus = 0;
										}
										if($bonusCap->num_rows() > 0){
											$bonusCapAmt = $bonusCap->row(0)->value;
										} else {
											$bonusCapAmt = 0;
										}
									}
									foreach($poChk->result() as $row){
										if($vicCorpID == 667531913){
											$bonusPay = $row->payoutAmount * $bonus;
											if($bonusPay <= $bonusCapAmt){
												$payout = $row->payoutAmount + $bonusPay;
											} else {
												$payout = $row->payoutAmount + $bonusCapAmt;
											}
										} else {
											$payout = $row->payoutAmount;
										}
										$payoutArr[$row->payoutType] = $payout;
									}
									$payouts = serialize($payoutArr);
									$dti = array('killID' => $killID,
												'killTime' => $killTime,
												'crest_link' => $crestLink,
												'bcast' => $bcastText,
												'fit' => serialize($fit),
												'attackers' => serialize($attackerArr),
												'victimName' => $vicName,
												'victimID' => $vicID,
												'corpID' => $vicCorpID,
												'corpName' => $vicCorpName,
												'shipID' => $vicShipTypeID,
												'shipName' => $vicShipTypeName,
												'sysID' => $sysID,
												'sysName' => $sysName,
												'regID' => $regID,
												'regName' => $regName,
												'secStatus' => $secStatus,
												'availablePayouts' => $payouts,
												'submittedBy' => $user,
												'ptQualified' => $ptQualified,
												'overPtCap' => $overPtCap);
									$this->db->insert('kills', $dti);
									echo "The kill has been submitted, thank you.";
								} else {
									echo "We are currently not reimbursing this ship type. If you feel this is in error, please contact the reimbursement team.";
								}	
							} else {
								echo "You are not currently in capswarm and thus not eligible for peace time reimbursement for a carrier.";
							}
						} else {
							echo "This loss is too old. You may only submit losses up to <b>" . $allowedDateDiff . "</b> days. Your loss is <b>" . $dayDiff."</b> days old.";
						}
					}
				} else {
					echo "You have not entered a valid CREST link, please try again.";
				}
			} else {
				echo "You are either not logged in, or you have been banned from reimbursement.";
			}
		} else {
			echo "We are not currently accepting losses, please check again later.";
		}
	}

	function myReserved(){
		if($this->isReim || $this->isReimDir){
			$user = $this->vars['user'];
			$data['reserved'] = $this->db->where('reservedBy', $user)->where('paid', '0')->get("kills");
			
			$this->load->view('header');
			$this->load->view('myReserved', $data);
			$this->load->view('footer');
		}
	}
	function claimBlock(){
		if($this->isReim || $this->isReimDir){
			$user = $this->vars['user'];
			$numRes = $this->input->post('numRes', TRUE);
			
			$rowsToRes = $this->db->where('paid', 0)->where('reservedBy', NULL)->order_by("timestamp",'ASC')->limit($numRes)->get('kills');
			if($rowsToRes->num_rows() > 0){
				foreach($rowsToRes->result() as $row){
					$dtu = array('reservedBy' => $user, 'reservedDate' => date('Y-m-d H:i:s'));
					$this->db->where('killID', $row->killID)->update('kills',$dtu);
				}
				echo "Your block has been reserved.";
			} else {
				echo "There are no losses to reserve.";
			}
		}
	}
	function releaseKill(){
		if($this->isReim || $this->isReimDir){
			$killID = $this->input->post('killID', TRUE);
			$user = $this->vars['user'];
			
			$dbChk = $this->db->where('killID', $killID)->get('kills');
			if($dbChk->num_rows() > 0){
				if($user == $dbChk->row(0)->reservedBy){
					if($dbChk->row(0)->paid == 0){
						$dtu = array('reservedBy' => NULL);
						$this->db->where('killID', $killID)->update('kills', $dtu);
					} else {
						echo "You cannot release a loss that has already been paid or denied.";
					}
				} else {
					echo "You are not allowed to release someone elses reserved loss. Fuck off.";
				}
			} else {
				echo "I could not find the kill specified, please try again. If the problem persists, please contact an admininstrator. " . $killID;
			}
		}
	}
	
	function mySubmitted(){
		if($this->logged_in){
			$data['submitted'] = $this->db->where('submittedBy',$this->vars['user'])->order_by('timestamp', 'DESC')->get('vwkills');
			
			$this->load->view('header');
			$this->load->view('mySubmitted', $data);
			$this->load->view('footer');
		}
	}
	
	function viewPayouts(){
		$payouts = $this->db->order_by('shipName', 'ASC')->get('vwpayoutTypeByShip');
		$data['payoutTypes'] = $this->db->select("DISTINCT(typeName) AS typeName")->get('vwpayoutTypeByShip');
		
		$payArr = array();
		foreach($payouts->result() as $row){
			$payArr[$row->shipName][$row->typeName] = $row->amount;
		}
		$data['payouts'] = $payArr;
		
		$this->load->view('header');
		$this->load->view('viewPayouts', $data);
		$this->load->view('footer');
	}
	function updateAvailablePayouts(){
		if($this->isReim || $this->isReimDir){
			$killID = $this->input->post('killID', TRUE);
			$killData = $this->db->where('killID', $killID)->get('kills');
			if($killData->num_rows() > 0){
				$vicCorpID = $killData->row(0)->corpID;
				$vicShipTypeID = $killData->row(0)->shipID;
				$poChk = $this->db->where('typeID', $vicShipTypeID)->get('payouts');
				$payoutArr = array();
				if($poChk->num_rows() > 0){
					if($vicCorpID == 667531913){
						$bonusamt = $this->db->where('name', 'waffeBonus')->get('adminSettings');
						$bonusCap = $this->db->where('name', 'waffeBonusCap')->get('adminSettings');
						if($bonusamt->num_rows() > 0){
							$bonus = $bonusamt->row(0)->value;
						} else {
							$bonus = 0;
						}
						if($bonusCap->num_rows() > 0){
							$bonusCapAmt = $bonusCap->row(0)->value;
						} else {
							$bonusCapAmt = 0;
						}
					}
					foreach($poChk->result() as $row){
						if($vicCorpID == 667531913){
							$bonusPay = $row->payoutAmount * $bonus;
							if($bonusPay <= $bonusCapAmt){
								$payout = $row->payoutAmount + $bonusPay;
							} else {
								$payout = $row->payoutAmount + $bonusCapAmt;
							}
						} else {
							$payout = $row->payoutAmount;
						}
						$payoutArr[$row->payoutType] = $payout;
					}
					$payouts = serialize($payoutArr);
					$dtu = array('availablePayouts' => $payouts);
					$this->db->where('killID', $killID)->update('kills', $dtu);
					
					echo "The payouts available have been updated.";
					
				} else {
					echo "There are no payout options available for this ship. No changes made.";
				}
			} else {
				echo "Something went wrong, could not find kill " . $killID;
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
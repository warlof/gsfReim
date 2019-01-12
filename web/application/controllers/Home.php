<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller
{

    /**
     * @var bool
     */
    private $logged_in;

    /**
     * @var bool
     */
    private $isReim;

    /**
     * @var bool
     */
    private $isReimDir;

    /**
     * @var bool
     */
    private $isBanned;

    /**
     * @var bool
     */
    private $isCapDir;

    /**
     * @var array
     */
    private $vars;

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    function __construct()
    {
        parent::__construct();
        $this->logged_in = false;
        $this->isReim = false;
        $this->isReimDir = false;
        $this->isBanned = false;
        $this->isCapDir = false;
        $this->vars = $this->session->userdata('vars');

        if ($this->vars['logged_in'] == true) {

            $this->logged_in = true;

            switch (1)
            {
                case $this->vars['isReim']:
                    $this->isReim = true;
                case $this->vars['isReimDir']:
                    $this->isReimDir = true;
                case $this->vars['isBanned']:
                    $this->isBanned = true;
                case $this->vars['isCapDir']:
                    $this->isCapDir = true;
            }

            $acceptLosses = $this->db->where('name', 'acceptLosses')->get('adminSettings');
            $this->accLosses = $acceptLosses->row(0)->value;
        }
    }

    function createAdmin()
    {
        if ($this->config->item('AUTH_METHOD') == "INTERNAL") {
            $dbchk = $this->db->where('user', 'admin')->get('users');
            if ($dbchk->num_rows() == 0) {
                $dti = [
                    'user'     => 'admin',
                    'password' => sha1($this->config->item('ADMIN_PASSWORD')),
                    'gids'     => '1,2,3',
                    'id'       => 1,
                ];

                $this->db->insert('users', $dti);

                echo "Admin user created.";
            } else {
                echo "The admin account already exists.";
            }
        } else {
            echo "This site is not configured to use internal auth.";
        }
    }

    public function index()
    {
        if ($this->logged_in) {
            $data['kills'] = $this->db->select("k.killID, k.bcast, k.submittedBy, k.corpName, a.allianceName, k.victimName, k.killTime, k.sysName, k.regName, k.shipName, k.attackers, k.fit, k.overPtCap")
                ->from("kills k")
                ->join("corporations c", "c.corpID = k.corpID", "left")
                ->join("alliances a", "a.allianceID = c.allianceID", "left")
                ->where("k.paid", 0)
                ->where("k.reservedBy", $this->session->userdata('vars')['user'])
                ->order_by("k.timestamp", "ASC")
                ->get();
            $data['allKills'] = $this->db->select("k.killID, k.bcast, k.submittedBy, k.corpName, a.allianceName, k.victimName, k.killTime, k.sysName, k.regName, k.shipName, k.attackers, k.fit, k.overPtCap")
                ->from("kills k")
                ->join("corporations c", "c.corpID = k.corpID", "left")
                ->join("alliances a", "a.allianceID = c.allianceID", "left")
                ->where("k.paid", 0)
                ->where("k.reservedBy", null)
                ->get();
        }

        $acceptLosses = $this->db->where('name', 'acceptLosses')->get('adminSettings');
        $data['acceptLosses'] = $acceptLosses->row(0)->value;

        $this->load->view('header');
        $this->load->view('home', $data);
        $this->load->view('footer');
    }

    function submitKill()
    {
        $user = $this->vars['user'];
        $carriers = [
            19720, 19722, 19724, 19726, 34339, 34341, 34343, 34345,
            23757, 23911, 23915, 24483, 37604, 37605, 37606, 37607,
        ];

        if ($this->accLosses == 1) {
            if ($this->logged_in && !$this->isBanned) {
                $crestLink = $this->input->post('crestLink', true);
                $bcastText = $this->input->post('bcast', true);
                $cD = $this->getKillData($crestLink);

                if ($cD['err']) {
                    echo "An error occurred: " . $cD['errMsg'];
                    return;
                }

                $crestData = $cD['data'];
                //Log the shit
                $dti = [
                    'user' => $this->session->userdata('vars')['user'],
                    'type' => 'kill_submit',
                    'data' => json_encode([
                        'IP'        => $this->input->ip_address(),
                        'KillID'    => $crestData['killID'],
                        'CrestLink' => $crestLink,
                    ]),
                ];

                $this->db->insert("ulog", $dti);

                $curDate = new DateTime("now");
                $killTime = str_replace('.', '-', $crestData['killTime']);
                $killDate = new DateTime($killTime);
                $diff = $curDate->diff($killDate);
                $dayDiff = $diff->format('%a');
                $aDd = $this->db->where('name', 'maxDayDiff')->get('adminSettings');
                $allowedDateDiff = $aDd->row(0)->value;

                if ($dayDiff <= $allowedDateDiff) {
                    $sysChk = $this->db->where('sys_eve_id', $crestData['sysID'])->get('vwsysconreg');
                    $regID = $sysChk->row(0)->reg_id;
                    $regName = $sysChk->row(0)->reg_name;
                    $secStatus = $sysChk->row(0)->sec;
                    $ptQualified = 0;
                    $overPtCap = 0;

                    $ptChk = $this->db->where('regID', $regID)->get("ptRegions");

                    if ($ptChk->num_rows() > 0) {
                        $ptQualified = 1;
                        $ptCap = $this->db->where('name', 'ptCap')->get('adminSettings');
                        $ptCapVal = $ptCap->row(0)->value;
                        $d = $killDate->format('Y-m');
                        $ptAmtChk = $this->db->where('submittedBy', $user)->where('month', $d)->get('vwptcap');

                        if ($ptAmtChk->num_rows() > 0 && $ptAmtChk->row(0)->total >= $ptCapVal) {
                            $overPtCap = 1;
                        }
                    }

                    //Check to see if the kill exists
                    $dbchk = $this->db->where('killID', $crestData['killID'])->get('kills');

                    if ($dbchk->num_rows() > 0) {
                        //It exists, so notify user as such
                        echo "This kill has already been submitted.";
                    } elseif (
                        (in_array($crestData['vicShipTypeID'], $carriers) && $this->vars['inCapSwarm'] == 1) ||
                        ! in_array($crestData['vicShipTypeID'], $carriers) ||
                        ! $this->config->item('RESTRICT_CAPITAL')) {
                        //Kill does NOT exist in db, so lets see if there is a payout set for this ship
                        $payoutArr = [];
                        $poChk = $this->db->select('p.payoutAmount, p.payoutType, p.typeID, p.id, p.typeName')
                                          ->from('payouts p')
                                          ->join('payoutTypes pt', 'pt.id = p.payoutType')
                                          ->where('p.typeID', $crestData['vicShipTypeID'])
                                          ->where('pt.active', '1')
                                          ->get();

                        if ($poChk->num_rows() > 0) {
                            if ($crestData['vicCorpID'] == 667531913) {
                                $bonus = 0;
                                $bonusCapAmt = 0;
                                $bonusamt = $this->db->where('name', 'waffeBonus')->get('adminSettings');
                                $bonusCap = $this->db->where('name', 'waffeBonusCap')->get('adminSettings');

                                if ($bonusamt->num_rows() > 0) {
                                    $bonus = $bonusamt->row(0)->value;
                                }

                                if ($bonusCap->num_rows() > 0) {
                                    $bonusCapAmt = $bonusCap->row(0)->value;
                                }
                            }

                            foreach ($poChk->result() as $row) {
                                $payout = $row->payoutAmount;

                                if ($crestData['vicCorpID'] == 667531913) {
                                    $bonusPay = $row->payoutAmount * $bonus;
                                    $payout = $row->payoutAmount + $bonusCapAmt;

                                    if ($bonusPay <= $bonusCapAmt) {
                                        $payout = $row->payoutAmount + $bonusPay;
                                    }
                                }

                                $payoutArr[$row->payoutType] = $payout;
                            }

                            $payouts = serialize($payoutArr);
                            $dti = [
                                'killID' => $crestData['killID'],
                                'killTime' => $crestData['killTime'],
                                'crest_link' => $crestLink,
                                'bcast' => $bcastText,
                                'fit' => serialize($crestData['fit']),
                                'attackers' => serialize($crestData['attackerArr']),
                                'victimName' => $crestData['vicName'],
                                'victimID' => $crestData['vicID'],
                                'corpID' => $crestData['vicCorpID'],
                                'corpName' => $crestData['vicCorpName'],
                                'shipID' => $crestData['vicShipTypeID'],
                                'shipName' => $crestData['vicShipTypeName'],
                                'sysID' => $crestData['sysID'],
                                'sysName' => $crestData['sysName'],
                                'regID' => $regID,
                                'regName' => $regName,
                                'secStatus' => $secStatus,
                                'availablePayouts' => $payouts,
                                'submittedBy' => $user,
                                'ptQualified' => $ptQualified,
                                'overPtCap' => $overPtCap
                            ];

                            $this->db->insert('kills', $dti);

                            //Add a thing to process the corporation info and insert that shit too.
                            $this->updateCorpData($crestData['vicCorpID']);
                            echo sprintf("The loss for %s in a %s has been submitted, thank you.", $crestData['vicName'], $crestData['vicShipTypeName']);
                        } else {
                            echo "We are currently not reimbursing this ship type. If you feel this is in error, please contact the reimbursement team.";
                        }
                    } else {
                        echo "You are not currently in capswarm and thus not eligible for peace time reimbursement for a carrier.";
                    }
                } else {
                    echo "This loss is too old. You may only submit losses up to <b>" . $allowedDateDiff . "</b> days. Your loss is <b>" . $dayDiff . "</b> days old.";
                }
            } else {
                echo "You are either not logged in, or you have been banned from reimbursement.";
            }
        } else {
            echo "We are not currently accepting losses, please check again later.";
        }
    }

    function myReserved()
    {
        if ($this->isReim || $this->isReimDir) {
            $user = $this->vars['user'];
            $data['reserved'] = $this->db->where('reservedBy', $user)->where('paid', '0')->get("kills");

            $this->load->view('header');
            $this->load->view('myReserved', $data);
            $this->load->view('footer');
        }
    }

    function claimBlock()
    {
        if ($this->isReim || $this->isReimDir) {
            $user = $this->vars['user'];
            $numRes = $this->input->post('numRes', true);

            $capGroups = [
                1538, 547, 485, 659, 30,
            ];

            $cData = $this->db->where_in("groupID", $capGroups)->get("invTypes");
            $caps = [];

            foreach ($cData->result() as $row) {
                $caps[] = $row->typeID;
            }

            if ($this->isCapDir) {
                $capsOnly = $this->input->post("capsOnly", true);
                if ($capsOnly == 1) {
                    $rowsToRes = $this->db->where("paid", 0)
                                          ->where("reservedBy", null)
                                          ->where_in("shipID", $caps)
                                          ->order_by("timestamp", "ASC")
                                          ->limit($numRes)
                                          ->get("kills");
                } else {
                    $rowsToRes = $this->db->where('paid', 0)
                                          ->where('reservedBy', null)
                                          ->order_by("timestamp", 'ASC')
                                          ->limit($numRes)
                                          ->get('kills');
                }
            } else {
                $rowsToRes = $this->db->where('paid', 0)
                                      ->where('reservedBy', null)
                                      ->where_not_in("shipID", $caps)
                                      ->order_by("timestamp", 'ASC')
                                      ->limit($numRes)
                                      ->get('kills');
            }

            if ($rowsToRes->num_rows() > 0) {
                foreach ($rowsToRes->result() as $row) {
                    $dtu = [
                        'reservedBy'   => $user,
                        'reservedDate' => date('Y-m-d H:i:s'),
                    ];

                    $this->db->where('killID', $row->killID)->update('kills', $dtu);
                }
                echo "Your block has been reserved.";
            } else {
                echo "There are no losses to reserve.";
            }
        }
    }

    function releaseKill()
    {
        if ($this->isReim || $this->isReimDir) {
            $killID = $this->input->post('killID', true);
            $user = $this->vars['user'];

            $dbChk = $this->db->where('killID', $killID)->get('kills');

            if ($dbChk->num_rows() > 0) {

                if ($user == $dbChk->row(0)->reservedBy) {

                    if ($dbChk->row(0)->paid == 0) {
                        $dtu = [
                            'reservedBy' => null,
                        ];

                        $this->db->where('killID', $killID)->update('kills', $dtu);

                        echo "The kill has been released.";
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

    function mySubmitted()
    {
        if ($this->logged_in) {
            $data['submitted'] = $this->db->where('submittedBy', $this->vars['user'])
                                          ->order_by('timestamp', 'DESC')
                                          ->get('vwkills');

            $this->load->view('header');
            $this->load->view('mySubmitted', $data);
            $this->load->view('footer');
        }
    }

    function viewPayouts()
    {
        $payouts = $this->db->order_by('shipName', 'ASC')->get('vwpayoutTypeByShip');
        $data['payoutTypes'] = $this->db->select("DISTINCT(typeName) AS typeName")->get('vwpayoutTypeByShip');

        $payArr = [];

        foreach ($payouts->result() as $row) {
            $payArr[$row->shipName][$row->typeName] = [
                'payout'    => $row->amount,
                'insProfit' => $row->insProfit,
                'totalReim' => $row->totalReim,
            ];
        }

        $data['payouts'] = $payArr;

        $this->load->view('header');
        $this->load->view('viewPayouts', $data);
        $this->load->view('footer');
    }

    function updateAvailablePayouts()
    {
        if ($this->isReim || $this->isReimDir) {
            $killID = $this->input->post('killID', true);
            $killData = $this->db->where('killID', $killID)->get('kills');

            if ($killData->num_rows() > 0) {
                $vicCorpID = $killData->row(0)->corpID;
                $vicShipTypeID = $killData->row(0)->shipID;
                $poChk = $this->db->where('typeID', $vicShipTypeID)->get('payouts');
                $payoutArr = [];

                if ($poChk->num_rows() > 0) {

                    if ($vicCorpID == 667531913) {
                        $bonus = 0;
                        $bonusCapAmt = 0;
                        $bonusamt = $this->db->where('name', 'waffeBonus')->get('adminSettings');
                        $bonusCap = $this->db->where('name', 'waffeBonusCap')->get('adminSettings');

                        if ($bonusamt->num_rows() > 0) {
                            $bonus = $bonusamt->row(0)->value;
                        }

                        if ($bonusCap->num_rows() > 0) {
                            $bonusCapAmt = $bonusCap->row(0)->value;
                        }
                    }

                    foreach ($poChk->result() as $row) {
                        $payout = $row->payoutAmount;

                        if ($vicCorpID == 667531913) {
                            $bonusPay = $row->payoutAmount * $bonus;
                            $payout = $row->payoutAmount + $bonusCapAmt;

                            if ($bonusPay <= $bonusCapAmt) {
                                $payout = $row->payoutAmount + $bonusPay;
                            }
                        }

                        $payoutArr[$row->payoutType] = $payout;
                    }

                    $payouts = serialize($payoutArr);
                    $dtu = [
                        'availablePayouts' => $payouts,
                    ];
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

    function updateCorpData($corpID)
    {
        //Get the public corp info from ESI, store it into `corporations` and then grab the public alliance data and store that as well.
        $corporation = $this->getCorporation($corpID);

        if ($corporation) {
            $allianceID = null;

            if (property_exists($corporation, 'alliance_id')) {
                $allianceID = $corporation->alliance_id;
            }

            $dti = [
                "corpID" => $corpID,
                "corpName" => $corporation->name,
                "allianceID" => $allianceID,
                "ticker" => $corporation->ticker,
            ];

            $this->db->replace("corporations", $dti);
        }

        if (! is_null($allianceID)) {
            $alliance = $this->getAlliance($allianceID);

            if ($alliance) {
                $dti = [
                    "allianceID" => $allianceID,
                    "allianceName" => $alliance->name,
                    "ticker" => $alliance->ticker,
                ];

                $this->db->replace("alliances", $dti);
            }
        }
    }

    function getKillData($link)
    {
        $ret = [
            'err' => false, 'errMsg' => '', 'data' => [],
        ];

        $fit = [];
        $low = [
            11, 12, 13, 14, 15, 16, 17, 18,
        ];
        $med = [
            19, 20, 21, 22, 23, 24, 25, 26,
        ];
        $high = [
            27, 28, 29, 30, 31, 32, 33, 34,
        ];
        $rigs = [
            92, 93, 94, 95, 96, 97, 98, 99,
        ];

        if (strpos($link, "esi") === false) {
            //Invalid link
            $ret['err'] = true;
            $ret['errMsg'] = "Invalid link provided.";
            return $ret;
        }

        //ESI Link
        $kill_mail = $this->curllib->makeRequest('GET', $link);
        $crestData = json_decode($kill_mail);

        if (isset($crestData->error)) {
            $ret['errMsg'] = "There was an error, please check your link and try again. If you continue to see this error, please contact an administrator.";
            $ret['err'] = TRUE;
            return $ret;
        }

        $character_ids = [];
        $corporation_ids = [];
        $alliance_ids = [];

        if (property_exists($crestData->victim, 'character_id'))
            array_push($character_ids, $crestData->victim->character_id);

        if (property_exists($crestData->victim, 'corporation_id'))
            array_push($corporation_ids, $crestData->victim->corporation_id);

        if (property_exists($crestData->victim, 'alliance_id'))
            array_push($alliance_ids, $crestData->victim->alliance_id);

        foreach ($crestData->attackers as $attacker) {
            if (property_exists($attacker, 'character_id'))
                array_push($character_ids, $attacker->character_id);
            if (property_exists($attacker, 'corporation_id'))
                array_push($corporation_ids, $attacker->corporation_id);
            if (property_exists($attacker, 'alliance_id'))
                array_push($alliance_ids, $attacker->alliance_id);
        }

        // resolving character, corporation and alliance names
        $character_names = $this->getCharacters($character_ids);
        $corporation_names = $this->getCorporations($corporation_ids);
        $alliance_names = $this->getAlliances($alliance_ids);

        // retrieving kill information
        $ret['data']['killID'] = $crestData->killmail_id;
        $ret['data']['killTime'] = $crestData->killmail_time;

        $ret['data']['sysID'] = $crestData->solar_system_id;
        $sysData = $this->getSystem($crestData->solar_system_id);
        $ret['data']['sysName'] = $sysData['solarSystemName'];

        // retrieve victim information
        $ret['data']['vicID'] = $crestData->victim->character_id;
        $ret['data']['vicName'] = $character_names[$crestData->victim->character_id];

        $ret['data']['vicShipTypeID'] = $crestData->victim->ship_type_id;
        $shipData = $this->getItem($crestData->victim->ship_type_id);
        $ret['data']['vicShipTypeName'] = $shipData['typeName'];

        $ret['data']['vicCorpID'] = $crestData->victim->corporation_id;
        $ret['data']['vicCorpName'] = $corporation_names[$crestData->victim->corporation_id];

        $ret['data']['damageTaken'] = $crestData->victim->damage_taken;

        $ret['data']['numAttackers'] = count($crestData->attackers);
        $attackerArr = [];
        $i = 1;

        // retrieving attackers information
        if (count($crestData->attackers) > 0) {
            foreach ($crestData->attackers as $a) {
                //
                // Alliance
                //
                $alliance = '';
                if (property_exists($a, 'alliance_id')) {
                    $alliance = 'Unknown';
                    if (array_key_exists($a->alliance_id, $alliance_names))
                        $alliance = $alliance_names[$a->alliance_id];
                }

                //
                // Ship
                //
                $ship_type = 'Unknown';
                if (property_exists($a, 'ship_type_id')) {
                    $shipData = $this->getItem($a->ship_type_id);

                    if (isset($shipData['typeName']))
                        $ship_type = $shipData['typeName'];
                }

                //
                // Character
                //
                $character = 'Unknown ' . $i;
                if (property_exists($a, 'character_id') &&
                    array_key_exists($a->character_id, $character_names)) {
                    $character = $character_names[$a->character_id];
                }

                //
                // Weapon
                //
                $weapon_type = 'Unknown';
                if (property_exists($a, 'weapon_type_id')) {
                    $weaponData = $this->getItem($a->weapon_type_id);

                    if (isset($weaponData['typeName']))
                        $weapon_type = $weaponData['typeName'];
                }

                //
                // Corporation
                //
                $corporation = 'Unknown Corporation';
                if (property_exists($a, 'corporation_id') &&
                    array_key_exists($a->corporation_id, $corporation_names)) {
                    $corporation = $corporation_names[$a->corporation_id];
                }

                //
                // Attacker
                //
                $attackerArr[$character] = [
                    'corporation' => $corporation,
                    'alliance'    => $alliance,
                    'shipType'    => $ship_type,
                    'weaponType'  => $weapon_type,
                    'damageDone'  => $a->damage_done,
                ];

                $i++;
            }

            $ret['data']['attackerArr'] = $attackerArr;
        }

        foreach ($crestData->victim->items as $item) {
            $itemData = $this->getItem($item->item_type_id);

            switch (true) {
                // High Slot
                case in_array($item->flag, $high):
                    $fit['high'][] = $itemData['typeName'];
                    break;
                // Medium Slot
                case in_array($item->flag, $med):
                    $fit['med'][] = $itemData['typeName'];
                    break;
                // Low Slot
                case in_array($item->flag, $low):
                    $fit['low'][] = $itemData['typeName'];
                    break;
                // Cargo Bay
                case($item->flag == 5):
                    $fit['cargo'][] = $itemData['typeName'];
                    break;
                // Rigs
                case in_array($item->flag, $rigs):
                    $fit['rigs'][] = $itemData['typeName'];
                    break;
                // Other
                default:
                    $fit['other'][] = $itemData['typeName'];
            }
        }

        $ret['data']['fit'] = $fit;

        return $ret;
    }

    function getCharacters(array $character_ids)
    {
        $data_array = [];
        $character_ids = array_unique($character_ids);
        $chunks = array_chunk($character_ids, 1000);

        log_message('debug', '$character_ids content => ' . print_r($character_ids, true));

        foreach ($chunks as $chunk) {
            $data = $this->curllib->makeRequest('POST', 'https://esi.evetech.net/v2/universe/names/', json_encode($chunk), [
                'Content-Type' => 'application/json',
            ]);
            $names = json_decode($data);

            if (! is_null($names)) {
                if (! is_array($names) && property_exists($names, 'error'))
                    throw new Exception('An error occurred during character names resolution.' . PHP_EOL .
                        print_r($names->error, true));

                foreach ($names as $name) {
                    if ($name->category == 'character')
                        $data_array[$name->id] = $name->name;
                }
            }
        }

        log_message('debug', '$character_names => ' . print_r($data_array, true));

        return $data_array;
    }

    function getCorporations(array $corporation_ids)
    {
        $data_array = [];
        $corporation_ids = array_unique($corporation_ids);
        $chunks = array_chunk($corporation_ids, 1000);

        log_message('debug', '$corporation_ids content => ' . print_r($corporation_ids, true));

        foreach ($chunks as $chunk) {
            $data = $this->curllib->makeRequest('POST', 'https://esi.evetech.net/v2/universe/names/', json_encode($chunk), [
                'Content-Type' => 'application/json',
            ]);
            $names = json_decode($data);

            if (! is_null($names)) {
                if (! is_array($names) && property_exists($names, 'error'))
                    throw new Exception('An error occurred during corporation names resolution.' . PHP_EOL .
                        print_r($names->error, true));

                foreach ($names as $name) {
                    if ($name->category == 'corporation')
                        $data_array[$name->id] = $name->name;
                }
            }
        }

        log_message('debug', '$corporation_names => ' . print_r($data_array, true));

        return $data_array;
    }

    function getCorporation($corpID) {
        $data = $this->curllib->makeRequest('GET', sprintf('https://esi.evetech.net/v4/corporations/%s/?datasource=tranquility&language=en-us',$corpID));
        $object = json_decode($data);

        if(! is_null($object)){
            return $object;
        }

        return false;
    }

    function getAlliances(array $alliance_ids)
    {
        $data_array = [];
        $alliance_ids = array_unique($alliance_ids);
        $chunks = array_chunk($alliance_ids, 1000);

        log_message('debug', '$alliance_ids content => ' . print_r($alliance_ids, true));

        foreach ($chunks as $chunk) {
            $data = $this->curllib->makeRequest('POST', 'https://esi.evetech.net/v2/universe/names/', json_encode($chunk), [
                'Content-Type' => 'application/json',
            ]);
            $names = json_decode($data);

            if (! is_null($names)) {
                if (! is_array($names) && property_exists($names, 'error'))
                    throw new Exception('An error occurred during alliance names resolution.' . PHP_EOL .
                        print_r($names->error, true));

                foreach ($names as $name) {
                    if ($name->category == 'alliance')
                        $data_array[$name->id] = $name->name;
                }
            }
        }

        log_message('debug', '$alliance_names => ' . print_r($data_array, true));

        return $data_array;
    }

    function getAlliance($allianceID) {
        $data = $this->curllib->makeRequest('GET', sprintf('https://esi.evetech.net/v3/alliances/%s/?datasource=tranquility&language=en-us',$allianceID));
        $object = json_decode($data);
        if(! is_null($object)){
            return $object;
        }

        return false;
    }

    function getItem($itemID)
    {
        $ret = [];
        $data = $this->db->where("typeID", $itemID)->get("invTypes");
        if ($data->num_rows() > 0) {
            $ret['typeID'] = $data->row(0)->typeID;
            $ret['groupID'] = $data->row(0)->groupID;
            $ret['typeName'] = $data->row(0)->typeName;
            $ret['marketGroupID'] = $data->row(0)->marketGroupID;

            return $ret;
        }

        return false;
    }

    function getSystem($sysID)
    {
        $ret = [];
        $data = $this->db->where("solarSystemID", $sysID)->get("mapSolarSystems");
        if ($data->num_rows() > 0) {
            $ret['regionID'] = $data->row(0)->regionID;
            $ret['constellationID'] = $data->row(0)->constellationID;
            $ret['solarSystemName'] = $data->row(0)->solarSystemName;

            return $ret;
        }

        return false;
    }
}

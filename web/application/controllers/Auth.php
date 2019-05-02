<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function index()
    {
        $state = uniqid(session_id(), true);

        $this->config->load('eve_sso');

        $sso_parameters = http_build_query([
            'response_type' => 'code',
            'redirect_uri'  => base_url('auth/callback'),
            'client_id'     => $this->config->item('eve_sso')['client_id'],
            'state'         => $state,
        ]);

        $sso_uri = sprintf('%s/authorize?%s', $this->config->item('eve_sso')['base_uri'], $sso_parameters);

        $_SESSION['eve_sso_state'] = $state;

        redirect($sso_uri);
    }

    public function callback()
    {
        if (! array_key_exists('eve_sso_state', $_SESSION))
            redirect('/', 'GET', 400);

        $expected_state = $_SESSION['eve_sso_state'];

        if ($this->input->get('state') !== $expected_state)
            redirect('/', 'GET', 400);

        if ($this->input->get('code') === '')
            redirect('/', 'GET', 400);

        // init authorize query
        $payload = $this->requestToken($this->input->get('code'));

        if (! $payload->access_token)
            redirect('/', 'GET', 401);

        $payload = $this->verifyToken($payload->access_token, $payload->token_type);

        $character_name = $payload->CharacterName;

        ///////// TO COMPLETE

        $this->load->model('User_model');
        $this->load->helper('string');

        $vars = [
            'err'        => false,
            'logged_in'  => false,
            'user'       => '',
            'isReim'     => 0,
            'isReimDir'  => 0,
            'inCapSwarm' => 0,
            'isCapDir'   => 0,
            'isBanned'   => 0,
            'groupData'  => [],
        ];

        $loginStats = $this->config->item("getStats");

        if ($loginStats) {
            $vars['accountId']  = 0;
            $vars['registered'] = '';
            $vars['lastPost']   = '';
            $vars['lastVisit']  = '';
            $vars['lastJabber'] = '';
            $vars['lastMumble'] = '';
        }

        $userData = $this->User_model->check_login($character_name, random_string('alnum', 64));

        log_message('debug', sprintf('Attempting login for %s: %s', $character_name, json_encode($userData)));

        if ($userData['err'] == false) {

            $banChk = $this->db->where('banEnd >', date('Y-m-d H:i:s'))->where('userName', $character_name)->get('bannedUsers');

            if ($banChk->num_rows() > 0) {
                $vars['isBanned']  = 1;
                $vars['banReason'] = $banChk->row(0)->reason;
                $vars['banEnd']    = $banChk->row(0)->banEnd;
            }

            $vars['logged_in']  = true;
            $vars['user']       = $character_name;
            $vars['isReim']     = $userData['isReim'];
            $vars['isReimDir']  = $userData['isReimDir'];
            $vars['inCapSwarm'] = $userData['inCapSwarm'];
            $vars['isCapDir']   = $userData['isCapDir'];

            if (isset($userData['groupData']))
                $vars['groupData'] = $userData['groupData'];

            if ($loginStats) {
                $vars['accountId']  = $userData['accountId'];
                $vars['registered'] = $userData['registered'];
                $vars['lastPost']   = $userData['lastPost'];
                $vars['lastVisit']  = $userData['lastVisit'];
                $vars['lastJabber'] = $userData['lastJabber'];
                $vars['lastMumble'] = $userData['lastMumble'];
            }

            $this->db->insert('ulog', [
                'user'	=> $character_name,
                'type'	=> 'LOGIN',
                'data'	=> 'IP : ' . $this->input->ip_address()
            ]);

        } else {
            $this->db->insert('ulog', [
                'user' => $character_name,
                'data' => sprintf('FAILED LOGIN ATTEMPT.<br>REASON: %s<br>MESSAGE: %s', $userData['errReason'], $userData['errMessage']),
                'type' => 'FAILED LOGIN',
            ]);

            $vars = array_merge($vars, $userData);
        }

        $this->session->set_userdata('vars', $vars);

        redirect('home');
    }

    /**
     * @param $authorization_code
     * @return mixed
     */
    private function requestToken($authorization_code)
    {
        // init authorize query
        $this->config->load('eve_sso');

        $authorization_uri = sprintf('%s/token', $this->config->item('eve_sso')['base_uri']);
        $authorization_body = sprintf('grant_type=authorization_code&code=%s', $authorization_code);
        $authorization_header = base64_encode(sprintf('%s:%s',
            $this->config->item('eve_sso')['client_id'], $this->config->item('eve_sso')['client_secret']));

        $response = $this->curllib->makeRequest('POST', $authorization_uri, $authorization_body, [
            sprintf('Authorization: Basic %s', $authorization_header),
            sprintf('Content-Type: %s', 'application/x-www-form-urlencoded'),
            sprintf('Host: %s',  'login.eveonline.com'),
        ]);

        return json_decode($response);
    }

    /**
     * @param $access_token
     * @param $token_type
     * @return mixed
     */
    private function verifyToken($access_token, $token_type)
    {
        // init verify query
        $response = $this->curllib->makeRequest('GET', 'https://esi.evetech.net/verify', '', [
            sprintf('Authorization: %s %s', $token_type, $access_token),
        ]);

        return json_decode($response);
    }
}

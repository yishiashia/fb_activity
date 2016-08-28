<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once( APPPATH . 'vendor/autoload.php');

Class Facebook {
	private $fb;
	private $api;
	public function __construct() {
		$this->api = & get_instance();
		$this->api->config->load('facebook');
		$this->api->load->library('session');
		$this->api->load->helper('url');

		$this->api->session->sess_expiration = 7200;
		$this->api->session->sess_expire_on_close = TRUE;

		$appid = $this->api->config->item('fb_app_id');
		$secret = $this->api->config->item('fb_app_secret');

		$this->fb = new Facebook\Facebook([
				'app_id' => $appid,
				'app_secret' => $secret,
				'default_graph_version' => 'v2.5',
		]);
	}

	public function is_login() {
		if(($result = $this->checkLogin()) === false) {
			return $this->checkLogin();
		} else {
			return true;
		}
	}

	private function checkLogin() {
		$helper = $this->fb->getJavaScriptHelper();
		try {
			if ($this->api->session->userdata('fb_access_token')) {
				$accessToken = $this->api->session->userdata('fb_access_token');
			} else {
				$accessToken = $helper->getAccessToken();
				$this->api->session->set_userdata('fb_access_token', $accessToken);
			}
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			//echo 'Graph returned an error: ' . $e->getMessage();
			$this->api->session->unset_userdata('fb_access_token');
			return false;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			//echo 'Facebook SDK returned an error: ' . $e->getMessage();
			$this->api->session->unset_userdata('fb_access_token');
			return false;
		}

		if(!isset($accessToken)) {
			//echo 'No cookie set or no OAuth data could be obtained from cookie.';
			$this->api->session->unset_userdata('fb_access_token');
			return false;
		} else {
			return true;
		}
	}

	public function profile() {
		if(($result = $this->doGetProfile()) === false) {
			if($this->checkLogin() !== false) {
				return $this->doGetProfile();
			} else {
				return false;
			}
		} else {
			return $result;
		}
	}

	public function doGetProfile() {
		if ($this->api->session->userdata('fb_access_token')) {
			$accessToken = $this->api->session->userdata('fb_access_token');
			try {
				// Returns a `Facebook\FacebookResponse` object
				$response = $this->fb->get('/me?fields=id,name,email', $accessToken);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				//echo 'Graph returned an error: ' . $e->getMessage();
				$this->api->session->unset_userdata('fb_access_token');
				return false;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				//echo 'Facebook SDK returned an error: ' . $e->getMessage();
				$this->api->session->unset_userdata('fb_access_token');
				return false;
			}

			$user = $response->getGraphUser();
			if($user) {
				return $user;
			} else {
				$this->api->session->unset_userdata('fb_access_token');
				return false;
			}
		} else {
			$this->api->session->unset_userdata('fb_access_token');
			return false;
		}
	}
}

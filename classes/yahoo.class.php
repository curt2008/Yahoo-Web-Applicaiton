<?php
	
	class Yahoo_API {
		
		private $oauth;
		private $token;
		private $config;
		private $ym;
	
		public function __construct() {
		}
		
		public function setConfig($config) {
			$this->config['consumer'] = $config['consumerKey'];
			$this->config['secret'] = $config['secretKey'];
			$this->config['y_username'] = $config['yahooUser'];
			$this->config['y_password'] = $config['yahooPass'];
		}
		
		public function getRequestToken() {	
			global $functions;
			
			$url = OAUTH_AUTH;
			$url .= '?login='. $this->config['y_username'];
			$url .= '&passwd='. $this->config['y_password'];
			$url .= '&oauth_consumer_key='. $this->config['consumer'];		
			$rs = $functions->curl($url);
			
			if (stripos($rs, 'RequestToken') === false) return false;
			$requesttoken = trim(str_replace('RequestToken=', '', $rs));
			$this->token['request'] = $requesttoken;
			return true;
		}
		
		public function getAccessToken() {
			global $functions;
			
			$this->signout();
			
			//prepare url
			$url = OAUTH_ACCESS;
			$url .= '?oauth_consumer_key='. $this->config['consumer'];		
			$url .= '&oauth_nonce='. uniqid(rand());
			$url .= '&oauth_signature='. $this->config['secret']. '%26';
			$url .= '&oauth_signature_method=PLAINTEXT';
			$url .= '&oauth_timestamp='. time();
			$url .= '&oauth_token='. $this->token['request'];
			$url .= '&oauth_version=1.0';	
			$rs = $functions->curl($url);
			
			if (stripos($rs, 'oauth_token') === false){
				return false;
			}
		
			//parse access token
			$tmp = explode('&', $rs);
			foreach ($tmp as $row){
				$col = explode('=', $row);
				$accesstoken[$col[0]] = $col[1];
			}
			$this->token['access'] = $accesstoken;
			return true;
		}
		
		public function signon($status = '', $state = 0) {
			global $functions;
			
			$this->signout();
			
			$url = YM_SESSION;
			$url .= '?oauth_consumer_key='. $this->config['consumer'];		
			$url .= '&oauth_nonce='. uniqid(rand());
			$url .= '&oauth_signature='. $this->config['secret']. '%26'. $this->token['access']['oauth_token_secret'];
			$url .= '&oauth_signature_method=PLAINTEXT';
			$url .= '&oauth_timestamp='. time();
			$url .= '&oauth_token='. $this->token['access']['oauth_token'];
			$url .= '&oauth_version=1.0&notifyServerToken=1';
			
			//additional header
			$header[] = 'Content-type: application/json; charset=utf-8';
			$postdata = '{"presenceState" : '. $state. ', "presenceMessage" : "'. $status. '"}';
			$rs = $functions->curl($url, 'post', $header, $postdata);
			
			$configArray = array();
			$jsonArray = json_decode($rs,true);
			foreach($jsonArray as $ymName => $ymValue) {
				$configArray[$ymName] = $ymValue;
			}
			
			if($configArray['sessionId'] == "") {
				return false;
			}
			
			/*
			returned values are :
				* sessionId = "JEt_fabs3B3r1Crs_PTorl.7wnxTZdbjRWXb"
				* primaryLoginId = "curtiscrewe30"
				* profileLoginIds {
					[0] {
						profileLoginId = "d5fde62e905c1c0d06cd3bde5bc81188"
					}
				}
				* displayInfo {
					avatarPreference = "0"
				}
				* server = "rcore3.messenger.yahooapis.com"
				* notifyServer = "rproxy3.messenger.yahooapis.com"
				* constants {
					presenceSubscriptionsMaxPerRequest = "500"
				}	
			*/
			$this->ym['signon'] = $configArray;
			return true;
		}
		
		public function signout() {
			global $functions;
			
			$url = YM_SESSION;
			$url .= '?oauth_consumer_key='. $this->config['consumer'];		
			$url .= '&oauth_nonce='. uniqid(rand());
			$url .= '&oauth_signature='. $this->config['secret']. '%26'. $this->token['access']['oauth_token_secret'];
			$url .= '&oauth_signature_method=PLAINTEXT';
			$url .= '&oauth_timestamp='. time();
			$url .= '&oauth_token='. $this->token['access']['oauth_token'];
			$url .= '&oauth_version=1.0';	
			$url .= '&sid='. $this->ym['signon']['sessionId'];

			//additional header
			$header[] = 'Content-type: application/json; charset=utf-8';	
			$rs = $functions->curl($url, 'delete', $header);

			return true;
		}
		
		public function PrimaryLogin() {
			$result = array(
				'username' => $this->ym['signon']['primaryLoginId'],
				'consumer' => $this->config['consumer'],
				'secret' => $this->config['secret'],
				'oauth_token' => $this->token['access']['oauth_token_secret'],
				'oauth_access' => $this->token['access']['oauth_token'],
				'sessionId' => $this->ym['signon']['sessionId'],
				'contactList' => $this->getContactList(),
				'primaryLogin' => $this->ym['signon']['primaryLogin'],
				'notifyServer' => $this->ym['signon']['notifyServer']
			);
			return json_encode($result);
		}
		
		public function getContactList() {
			global $functions;
			
			$url = YM_CONTACT;
			$url .= '?oauth_consumer_key='. $this->config['consumer'];		
			$url .= '&oauth_nonce='. uniqid(rand());
			$url .= '&oauth_signature='. $this->config['secret']. '%26'. $this->token['access']['oauth_token_secret'];
			$url .= '&oauth_signature_method=PLAINTEXT';
			$url .= '&oauth_timestamp='. time();
			$url .= '&oauth_token='. $this->token['access']['oauth_token'];
			$url .= '&oauth_version=1.0';	
			$url .= '&sid='. $this->ym['signon']['sessionId'];
			$url .= '&fields=%2Bpresence';
			$url .= '&fields=%2Bgroups';
			
			//additional header
			$header[] = 'Content-type: application/json; charset=utf-8';		
			$rs = $functions->curl($url, 'get', $header);
				
			if (stripos($rs, 'contact') === false) return false;
		
			$js = json_decode($rs,true);
				
			return $js['contacts'];
		}
		
		public function getSessionId() {
			return $this->ym['signon']['sessionId'];
		}
		
		public function getStatus($user) {
			global $functions;
			
			$url = "http://opi.yahoo.com/online";
			$url .= '?u='. $user;		
			$url .= '&m=k&t=1';
			
			$rs = $functions->curl($url);
			switch($rs) {
				case "00":
					return "Offline";
					break;
				case "01":
					return "Online";
					break;
			}
		}
		
		public function send_message($data) {
			global $functions;
			
			$url = YM_MESSAGE;
			$url .= '?oauth_consumer_key='. $_SESSION['consumer'];		
			$url .= '&oauth_nonce='. uniqid(rand());
			$url .= '&oauth_signature='. $_SESSION['secret']. '%26'. $_SESSION['oauth_token'];
			$url .= '&oauth_signature_method=PLAINTEXT';
			$url .= '&oauth_timestamp='. time();
			$url .= '&oauth_token='. $_SESSION['oauth_access'];
			$url .= '&oauth_version=1.0';	
			$url .= '&sid='. $_SESSION['sessionId'];
			$url = str_replace('{{USER}}', $data['user'], $url);
			
			//additional header
			$header[] = 'Content-type: application/json; charset=utf-8';
			$postdata = '{"message" : "'. str_replace('"', '\"', $data[message]) . '"}';
			
			$rs = $functions->curl($url, 'post', $header, $postdata);
			return $rs;
		}
		
		public function fetch_long_notification($seq = 1) {		
			global $functions;
			
			$url = YM_LONGPOLL;
			$url .= '?oauth_consumer_key='. $_SESSION['consumer'];	
			$url .= '&oauth_nonce='. uniqid(rand());
			$url .= '&oauth_signature='. $_SESSION['secret']. '%26'. $_SESSION['oauth_token'];
			$url .= '&oauth_signature_method=PLAINTEXT';
			$url .= '&oauth_timestamp='. time();
			$url .= '&oauth_token='. $_SESSION['oauth_access'];
			$url .= '&oauth_version=1.0';	
			$url .= '&sid='. $_SESSION['sessionId'];
			$url .= '&seq='. intval($seq);
			$url .= '&format=json';
			$url .= '&count=100';
			$url .= '&idle=120';
			$url .= '&rand='. uniqid(rand());

			$url = str_replace('{{NOTIFICATION_SERVER}}', $_SESSION['notifyServer'], $url);
			$url = str_replace('{{USER}}', $_SESSION['primaryLogin'], $url);
			
			//additional header
			$header[] = 'Content-type: application/json; charset=utf-8';
			$header[] = 'Connection: keep-alive';
			$rs = $functions->curl($url, 'get', $header, null, 160);
			
			$js = json_decode($rs, true);

			if(isset($js['error'])){
				return false;
			}
			
			return var_dump($js);
			//return $js['responses'];
		}
		
		public function fetch_notification($seq = 0) {		
			global $functions;
			
			$url = YM_NOTIFICATION;
			$url .= '?oauth_consumer_key='. $_SESSION['consumer'];		
			$url .= '&oauth_nonce='. uniqid(rand());
			$url .= '&oauth_signature='. $_SESSION['secret']. '%26'. $_SESSION['oauth_token'];
			$url .= '&oauth_signature_method=PLAINTEXT';
			$url .= '&oauth_timestamp='. time();
			$url .= '&oauth_token='. $_SESSION['oauth_access'];
			$url .= '&oauth_version=1.0';	
			$url .= '&sid='. $_SESSION['sessionId'];
			$url .= '&seq='. intval($seq);
			$url .= '&count=100';

			//additional header
			$header[] = 'Content-type: application/json; charset=utf-8';
			$rs = $functions->curl($url, 'get', $header);
			
			$js = json_decode($rs, true);
				
			if (isset($js['error'])){
				return "Error on JSON";
			}
			
			$messages = array();
			
			if(count($js['responses']) > 0){
				$response = $js['responses'];
				foreach($response as $key => $data) {
    					if (array_key_exists('message', $data)) {
        					$msgArray = $data['message'];
        					$messages[] = $msgArray;
    					}
				}
			} else {$messages[] = "";}
			echo json_encode($messages);
		}

	
	}
	
?>
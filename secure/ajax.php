<?php
	
	require $_SERVER['DOCUMENT_ROOT']."/secure/core.php";
	
	switch($_GET['act']) {
		
		case "login":
			$config['consumerKey'] = consumerKey;
			$config['secretKey'] = secretKey; 
			$config['yahooUser'] = $_GET['username'];
			$config['yahooPass'] = $_GET['password'];
			
			$yahoo->setConfig($config);
			if($yahoo->getRequestToken()) {
				if($yahoo->getAccessToken()) {
					if($yahoo->signon()) {
						$primaryLogin = json_decode($yahoo->PrimaryLogin(),true);
						
						$_SESSION['yahooUser'] = $_GET['username'];
						$_SESSION['yahooPass'] = $_GET['password'];
						$_SESSION['sessionId'] = $primaryLogin['sessionId'];
						$_SESSION['oauth_access'] = $primaryLogin['oauth_access'];
						$_SESSION['oauth_token'] = $primaryLogin['oauth_token'];
						$_SESSION['consumer'] = $primaryLogin['consumer'];
						$_SESSION['secret'] = $primaryLogin['secret'];
						$_SESSION['primaryLogin'] = $primaryLogin['primaryLogin'];
						$_SESSION['notifyServer'] = $primaryLogin['notifyServer'];
						
						$result = array(
							'primaryLogin' => $yahoo->PrimaryLogin(),
							'result' => '200'	
						);
					}else{$result = array('result' => '41', 'code' => 'Failed to Login');}
				}else{$result = array('result' => '42', 'code' => 'Failed to get Access Token');}
			}else{$result = array('result' => '43', 'code' => 'Failed to request Token');}
			
			echo json_encode($result);
			break;
			
		case "chat":
			$data['user'] = $_GET['user'];
			$data['message'] = $_GET['msg'];
			
			echo $yahoo->send_message($data);
			break;
			
		case "status":
			echo $yahoo->getStatus($_GET['user']);
			break;
			
		case "longpoll":
			echo $yahoo->fetch_notification();
			break;
	}
	
?>
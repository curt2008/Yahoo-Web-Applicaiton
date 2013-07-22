<?php
	
	session_start();
	$config = array();
	
	ini_set('session.bug_compat_warn', 0);
	ini_set('session.bug_compat_42', 0);
	
	define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
	define("PHP_CLASS", $_SERVER['DOCUMENT_ROOT']."/classes/");
	define("SECURE", $_SERVER['DOCUMENT_ROOT']."/secure/");
	
	//define global API URLs
	define("OAUTH_AUTH", "https://login.yahoo.com/WSLogin/V1/get_auth_token");
	define("OAUTH_ACCESS", "https://api.login.yahoo.com/oauth/v2/get_token");
	define("YM_SESSION", "http://developer.messenger.yahooapis.com/v1/session");
	define("YM_CONTACT", "http://developer.messenger.yahooapis.com/v1/contacts");
	define("YM_MESSAGE", "http://developer.messenger.yahooapis.com/v1/message/yahoo/{{USER}}");
	define("YM_LONGPOLL", "http://{{NOTIFICATION_SERVER}}/v1/pushchannel/{{USER}}");
	define("YM_NOTIFICATION", "http://developer.messenger.yahooapis.com/v1/notifications");
	
	//require config
	require_once SECURE."config.php";
	
	//global CONFIG
	define("consumerKey", $cfg['consumerKey']);
	define("secretKey", $cfg['secretKey']);
	
	//requre classes
	require_once PHP_CLASS."yahoo.class.php";
	require_once PHP_CLASS."functions.class.php";
	
	//initialize classes
	$yahoo = new Yahoo_API();
	$functions = new Functions();
?>
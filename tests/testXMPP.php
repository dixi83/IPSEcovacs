<?php

$key = 'MIIB/TCCAWYCCQDJ7TMYJFzqYDANBgkqhkiG9w0BAQUFADBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMCAXDTE3MDUwOTA1MTkxMFoYDzIxMTcwNDE1MDUxOTEwWjBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDb8V0OYUGP3Fs63E1gJzJh+7iqeymjFUKJUqSD60nhWReZ+Fg3tZvKKqgNcgl7EGXp1yNifJKUNC/SedFG1IJRh5hBeDMGq0m0RQYDpf9l0umqYURpJ5fmfvH/gjfHe3Eg/NTLm7QEa0a0Il2t3Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBANhIMT0+IyJa9SU8AEyaWZZmT2KEYrjakuadOvlkn3vFdhpvNpnnXiL+cyWy2oU1Q9MAdCTiOPfXmAQt8zIvP2JC8j6yRTcxJCvBwORDyv/uBtXFxBPEC6MDfzU2gKAaHeeJUWrzRv34qFSaYkYta8canK+PSInylQTjJK9VqmjQ';

$ckey 	= 'eJUWrzRv34qFSaYk';
$secret = 'Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GC';

include('/var/lib/symcon/externalscripts/Crypt/Crypt/RSA.php');
include('/var/lib/symcon/externalscripts/Crypt/File/X509.php');
include('/var/lib/symcon/externalscripts/XMPPHP/XMPP.php');

$username	= 'iets@niets.com'; 
$password	= 'md5_converted_password_000000000';

$meta['country']		= 'nl';
$meta['lang']			= 'en';
$meta['appCode']		= 'i_eco_e';
$meta['appVersion']		= '1.3.5';
$meta['channel']		= 'c_googleplay';
$meta['deviceType']		= '1';
$meta['account']		= encrypt($username);
$meta['password']		= encrypt($password);
$meta['authTimespan']	= round(microtime(true)*1000);
$meta['authTimeZone']	= 'GMT-8';
$meta['deviceId']		= md5(time()/5); // must be "realy" random
$meta['resource']		= substr($meta['deviceId'], 0, 8);
$meta['continent']		= 'eu';
$meta['authAppkey']		= $ckey;
$meta['realm']			= 'ecouser.net';

$function['login']			= 'user/login';
$function['getAuthCode']	= 'user/getAuthCode';
$function['loginByItToken']	= 'loginByItToken';

EcoVacsHTTPS_Login($meta);
//print_r($meta);
echo $meta['accessToken'].'
';
EcoVacsHTTPS_getAuthCode($meta);
echo $meta['authCode'].'
';
EcoVacsHTTPS_loginByItToken($meta);
echo $meta['token'].'
';
$XMPP = json_encode(EcoVacsHTTPS_GetDeviceList($meta));

$XMPP_ID = 50404 /*[Testhoek\Deebot\XMPP_Info]*/;
SetValue($XMPP_ID, $XMPP);

//print_r($XMPP);

$XMPP = json_decode($XMPP,true);

EcoVacsXMPP_SendCommand2($XMPP, 0, '');

// Functions:
function EcoVacsHTTPS_Login(&$meta){
	global $function;
	
	$meta['requestId']	= md5(round(microtime(true)*1000));	
	$meta['requestId']	= $meta['requestId'];
	
	$MAIN_URL_FORMAT = 'https://eco-'.$meta['country'].'-api.ecovacs.com/v1/private/'.$meta['country'].'/'.$meta['lang'].'/'.$meta['deviceId'].'/'.$meta['appCode'].'/'.$meta['appVersion'].'/'.$meta['channel'].'/'.$meta['deviceType'];
	
	$order 				= array('account','appCode','appVersion','authTimeZone','authTimespan','channel','country','deviceId','deviceType','lang','password','requestId');
	$info4Sign 			= orderArray($order, $meta);	
	$authSign 			= sign($info4Sign);
	$meta['authSign']	= md5($authSign);

	$order 		= array('account','password','requestId','authTimespan','authTimeZone','authAppkey','authSign');
	$info4Url 	= orderArray($order, $meta);	
	$query 		= "?".http_build_query($info4Url, '', '&');	
	$url	 	= $MAIN_URL_FORMAT.'/'.$function['login'].$query;
	
	$response = file_get_contents($url);
	
	if($response==false) {
		echo 'Error! no connection or URL is wrong.\n';
		echo 'URL: '.$url.'
';
		echo 'authSign: '.$authSign;
		return false;
	} else {
		$return = json_decode($response,true);
		if($return['code']!='0000') {
			echo 'Login Error! '.showMsg($return['code']).'
';
			echo 'URL: '.$url.'
';
			echo 'authSign: '.$authSign;
			return false;
		} else {
			unset($meta['requestId']);
			$meta = array_merge($meta,$return['data']);
			return $return;
		}
	}
}

function EcoVacsHTTPS_getAuthCode(&$meta){
	global $function;

	$meta['requestId']	= md5(round(microtime(true)*1000));
		
	$meta['requestId']	= $meta['requestId'];
	
	$MAIN_URL_FORMAT = 'https://eco-'.$meta['country'].'-api.ecovacs.com/v1/private/'.$meta['country'].'/'.$meta['lang'].'/'.$meta['deviceId'].'/'.$meta['appCode'].'/'.$meta['appVersion'].'/'.$meta['channel'].'/'.$meta['deviceType'];

	$order 				= array('accessToken','appCode','appVersion','authTimeZone','authTimespan','channel','country','deviceId','deviceType','lang','requestId','uid');
	$info4Sign			= orderArray($order, $meta);
	$authSign 			= sign($info4Sign);
	$meta['authSign']	= md5($authSign);
	
	
	$order 		= array('uid','accessToken','requestId','authTimespan','authTimeZone','authAppkey','authSign');
	$info4Url 	= orderArray($order, $meta);
	$query 		= "?".http_build_query($info4Url, '', '&');	
	$url	 	= $MAIN_URL_FORMAT.'/'.$function['getAuthCode'].$query;
	
	$response = file_get_contents($url);
	
	if($response==false) {
		echo 'Error! no connection or URL is wrong.';
		return false;
	} else {
		$return = json_decode($response,true);
		if($return['code']!='0000') {
			echo 'Error! '.showMsg($return['code']).'
';
			echo 'URL: '.$url.'
';
			echo 'authSign: '.$authSign;
			return false;
		} else {
			unset($meta['requestId']);
			$meta = array_merge($meta,$return['data']);
			return $return;
		}
	}
}

function EcoVacsHTTPS_loginByItToken(&$meta){
	global $function;

	$USER_URL_FORMAT = 'https://users-'.$meta['continent'].'.ecouser.net:8000/user.do';
	
	$ch = curl_init($USER_URL_FORMAT);
	
	$meta['todo'] 		= 'loginByItToken';

	$order 		= array('authCode','realm','uid','resource','todo','country');
	$info4Post 	= orderArray($order, $meta);
	$newKeys	= array('token','realm','userId','resource','todo','country');
	$info4Post	= renameKeysInArray($order,$newKeys,$info4Post);
	
	$info4Post['country'] = strtoupper($info4Post['country']);
	
	$json_str = json_encode($info4Post);
	 
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_str);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	$result = curl_exec($ch);
	curl_close($ch);
	
	if($result==false) {
		echo 'Error! no connection or URL is wrong.';
		return false;
	} else {
		$return = json_decode($result,true);
		if($return['result']!='ok') {
			echo 'Error! '.$return['error'];
			return false;
		} else {
			$meta['token'] = $return['token'];
			return $return;
		}
	}
}

function EcoVacsHTTPS_GetDeviceList(&$meta){
	global $function;

	$USER_URL_FORMAT = 'https://users-'.$meta['continent'].'.ecouser.net:8000/user.do';
	
	$ch = curl_init($USER_URL_FORMAT);
	
	$meta['todo'] 		= 'GetDeviceList';
	$meta['with'] 		= 'users';

	$order			= array('with','realm','token','uid','resource');
	$auth	 		= orderArray($order, $meta);
	$newKeys		= array('with','realm','token','userid','resource');
	$meta['auth']	= renameKeysInArray($order,$newKeys,$auth);
	
	$order 		= array('todo','uid','auth');
	$info4Post 	= orderArray($order, $meta);
	$newKeys	= array('todo','userid','auth');
	$info4Post	= renameKeysInArray($order,$newKeys,$info4Post);
	
	$json_str = json_encode($info4Post);
	
	//print_r($json_str);
	
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_str);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	$result = curl_exec($ch);
	curl_close($ch);
	
	if($result==false) {
		echo 'Error! no connection or URL is wrong.';
		return false;
	} else {
		$return = json_decode($result,true);
		if($return['result']!='ok') {
			echo 'Error! '.$return['error'];
			return false;
		} else {
			$XMPP['username'] 	= $meta['uid'].'@'.$meta['realm'];
			$XMPP['password'] 	= '0/'.$meta['resource'].'/'.$meta['token'];
			$XMPP['continent']	= $meta['continent'];
			$XMPP['resource']	= $meta['resource'];
			$XMPP['domain']		= $meta['realm'];
			
			$i = 0;
			//print_r($return);
			foreach($return['devices'] as $value){
				$XMPP['robot'][$i] = $return['devices'][$i]['did'].'@'.$return['devices'][$i]['class'].'.ecorobot.net/'.$return['devices'][$i]['resource'];
				++$i;
			}
			return $XMPP;
		}
	}
}

function EcoVacsXMPP_SendCommand($XMPP, $robotNr, $command) {

	$set['server'] 		= 'msg-'.$XMPP['continent'].'.ecouser.net'; 	//'msg-'.$glb_continent.'.ecouser.net';
	$set['port']		= 5223;
	$set['username']	= $XMPP['username'];			//sucks      DEBUG    username used to login: 201802265a9437ee73aa7@ecouser.net
	$set['password']	= $XMPP['password'];			//sucks      DEBUG    password used to login: 0/372d00ce/glcTBbzoppbndSRpTflNTpk1gDCAYLQv
	$set['resource']	= $XMPP['resource'];
	$set['domain']		= $XMPP['domain'];
	$set['vacAddr']		= $XMPP['robot'][$robotNr];		//self.vacuum['did'] + '@' + self.vacuum['class'] + '.ecorobot.net/atom'
	
	print_r($set);
	
	// Send:
	$conn = new XMPPHP_XMPP($set['server'], $set['port'], $set['username'], $set['password'], $set['resource'], $set['domain'], $printlog = true, $loglevel = XMPPHP_Log::LEVEL_VERBOSE);
	$conn->useEncryption(false);
	try {
		echo "connect() start
";
		$conn->connect();
		echo "processUntil() start
";
		$conn->processUntil(array('session_start'));
		echo "presence() start
";
		$conn->presence();
		$conn->message($set['vacAddr'], '<query xmlns="com:ctl"><ctl td="GetChargeState" />');
		$conn->disconnect();
	} catch (XMPPHP_Exception $e) {
		die($e->getMessage());
	}
	
	return;
	
	// Receive Answer
	$conn = new XMPPHP_XMPP($set['server'], $set['port'], $set['password'], $resource);
	//$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'username', 'password', 'xmpphp', 'gmail.com', $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);
	$conn->autoSubscribe();
	$vcard_request = array();
	try {
	    $conn->connect();
	    while(!$conn->isDisconnected()) {
	    	$payloads = $conn->processUntil(array('message', 'presence', 'end_stream', 'session_start', 'vcard'));
	    	foreach($payloads as $event) {
	    		$pl = $event[1];
	    		print_r($pl);
	    		}
	    	}
	} catch(XMPPHP_Exception $e) {
	    die($e->getMessage());
	}
}

function encrypt($plaintext) {
	global $key;
	
	$key = "-----BEGIN CERTIFICATE-----\r\n" . chunk_split($key) . "\r\n-----END CERTIFICATE-----";

	$x509 = new File_X509();
	$x509->loadX509($key);
	$pkey = $x509->getPublicKey();
	
    openssl_public_encrypt( $plaintext , $result , $pkey );
	
    $result = base64_encode($result);
	return $result;	
	
} // end of function encrypt

function sign($meta) {
	global $ckey, $secret;

	ksort($meta);
	
	$string = '';
	
	foreach($meta as $key => $value) {
		$string = $string.$key.'='.$value;
	}

	return $ckey.$string.$secret;
}

function orderArray($order, $array) {
	foreach($order as $value) {
		$return[$value] = $array[$value];
	}
	return $return;
}

function renameKeysInArray($oldNames, $newNames, $array) {
	foreach($oldNames as $key => $value) {
		$return[$newNames[$key]] = $array[$value];
	}
	return $return;
}


// Below only functions for testing purpose:

function url4compare($url) {
	//%2B = '+', %2F = '/' en %3D = '='
	$inUrl = array('%2B', '%2F', '%3D');
	$real  = array('+',   '/',   '=');
	
	return str_replace($inUrl,$real,$url);
}

function showMsg($nr) {
	$code['0000'] = 'login OK';
	$code['0001'] = 'operation failed';
	$code['0002'] = 'interface authentication failed';
	$code['0003'] = 'abnormal parameter';
	$code['1005'] = 'wrong username/password';
	$code['9001'] = 'Authorization code expired!';
	
	return $nr.': '.$code[$nr];
}

		
?>
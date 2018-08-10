<?

//include('/var/lib/symcon/externalscripts/XMPPHP/BOSH.php');
include('/var/lib/symcon/externalscripts/XMPPHP/XMPP.php');

$glb_user 		= "iets@niets.com";
$glb_pwd 		= "eenWW";
$glb_country 	= "NL";
$glb_continent 	= "eu";
$glb_resource 	=  substr(md5(time()), 0, 8);

echo $glb_resource;

/*function EcoVacsHTTP_Step1FindHost() { // returns false on failure or an array containing $array['result'], $array['ip'], $array['port']

	$url = 'https://lbnl.ecouser.net:8006/lookup.do';

	$ch = curl_init($url);
		
	$json['todo'] 		= 'FindBest';
	$json['service']	= 'EcoUserNew';
	
	$json_str = json_encode($json);
	 
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
		return $return;
	}
}*/

function EcoVacsHTTP_Login() {
	global $glb_user;
	global $glb_country;
	global $glb_pwd;
	global $glb_resource;

	$url = "https://lbnl.ecouser.net:8000/user.do";
	
	$ch = curl_init($url);
	
	$json['todo'] 		= 'getVipUserId';
	$json['loginName']	= $glb_user;
	$json['country']	= $glb_country;
	
	$json_str = json_encode($json);
	 
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
		$returnLogin = json_decode($result,true);
		if($returnLogin['result']!='ok') {
			echo 'Error! '.$returnLogin['error'];
			return false;
		} else {
			$url = "https://lbnl.ecouser.net:8000/user.do";
			
			$ch = curl_init($url);
			
			$json['todo'] 		= 'login';
			$json['meId']		= $returnLogin['userId'];
			$json['resource']	= $glb_resource;
			$json['last']		= '';
			$json['password']	= md5($glb_pwd);
			$json['country']	= $glb_country;
			$json['realm']		= 'ecouser.net';
			
			$json_str = json_encode($json);
			 
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
					$return['userId'] = $returnLogin['userId'];
					return $return;
				}
			}
		}
	}
}

function EcoVacsHTTP_GetAuthCode() {
	// SENT: loginByItToken with {'resource': '372d00ce', 'realm': 'ecouser.net', 'userId': '201802265a9437ee73aa7', 'country': 'NL', 'token': 'nl_73f4b153fda39e0c2274383e1348efd3'}
	// RECV: 
	global $glb_user;
	global $glb_country;
	global $glb_pwd;
	global $glb_resource;

	$url = "https://lbnl.ecouser.net:8000/user.do";
	
	$ch = curl_init($url);
	
	$json['todo'] 		= 'loginByItToken';
	$json['realm']		= 'ecouser.net';
	$json['loginName']	= $glb_user;
	$json['country']	= $glb_country;
	
	$json_str = json_encode($json);
	 
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
		echo "ohoh";
	}
}


function EcoVacsHTTP_GetToken() {
	// SENT: loginByItToken with {'resource': '372d00ce', 'realm': 'ecouser.net', 'userId': '201802265a9437ee73aa7', 'country': 'NL', 'token': 'nl_73f4b153fda39e0c2274383e1348efd3'}
	// RECV: 
	global $glb_user;
	global $glb_country;
	global $glb_pwd;
	global $glb_resource;

	$url = "https://lbnl.ecouser.net:8000/user.do";
	
	$ch = curl_init($url);
	
	$json['todo'] 		= 'loginByItToken';
	$json['realm']		= 'ecouser.net';
	$json['loginName']	= $glb_user;
	$json['country']	= $glb_country;
	
	$json_str = json_encode($json);
	 
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
		echo "ohoh";
	}
}


function EcoVacsHTTP_GetDeviceList($uid, $token, $resource) {
	$url = 'https://lbnl.ecouser.net:8000/user.do';
	
	$ch = curl_init($url);
	
	$json['todo'] 				= 'GetDeviceList';
	$json['userid']				= $uid;
	$json['auth']['with']		= 'users';
	$json['auth']['userid']		= $uid;
	$json['auth']['realm']		= 'ecouser.net';
	$json['auth']['token']		= $token;
	$json['auth']['resource']	= $resource;	
			
	
	$json_str = json_encode($json);
	
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
			return $return;
		}
	}
}
/*
function EcoVacsXMPP_SendCommand($uid, $token, $resource, $vac_did, $vac_class, $command) {
	global $glb_continent;
	
	$set['server'] 		= 'lbnl.ecouser.net'; 								//'msg-'.$glb_continent.'.ecouser.net';
	$set['port']		= 5223;
	$set['user']		= $uid.'@ecouser.net';								//sucks      DEBUG    username used to login: 201802265a9437ee73aa7@ecouser.net
	$set['password']	= '0/'.$resource.'/'.$token;						//sucks      DEBUG    password used to login: 0/372d00ce/glcTBbzoppbndSRpTflNTpk1gDCAYLQv
	$set['vacAddr']		= $vac_did.'@'.$vac_class.'.ecorobot.net/atom';		//self.vacuum['did'] + '@' + self.vacuum['class'] + '.ecorobot.net/atom'
	
	print_r($set);
	
	// Send:
	$conn = new XMPPHP_XMPP($set['server'], $set['port'], $set['user'], $set['password'], 'ecouser.net', NULL, $printlog = true, $loglevel = XMPPHP_Log::LEVEL_VERBOSE);
	
	//try {
		$conn->connect();
		$conn->processUntil(array('session_start', 'unknown domain'));
		//$conn->presence();
		//$conn->message($set['vacAddr'], '<query xmlns="com:ctl"><ctl td="GetChargeState" />');
		$conn->disconnect();
	//} catch (XMPPHP_Exception $e) {
	//	die($e->getMessage());
	//}
	
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
}*/

$step2 = EcoVacsHTTP_Login();
print_r($step2);

$step4 = EcoVacsHTTP_GetDeviceList($step2['userId'], $step2['token'],$step2['resource']);
print_r($step4);

//EcoVacsXMPP_SendCommand($step2['userId'], $step2['token'], $step2['resource'], $step4['devices'][0]['did'], $step4['devices'][0]['class'], "")

?>
<?php

$key = 'MIIB/TCCAWYCCQDJ7TMYJFzqYDANBgkqhkiG9w0BAQUFADBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMCAXDTE3MDUwOTA1MTkxMFoYDzIxMTcwNDE1MDUxOTEwWjBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDb8V0OYUGP3Fs63E1gJzJh+7iqeymjFUKJUqSD60nhWReZ+Fg3tZvKKqgNcgl7EGXp1yNifJKUNC/SedFG1IJRh5hBeDMGq0m0RQYDpf9l0umqYURpJ5fmfvH/gjfHe3Eg/NTLm7QEa0a0Il2t3Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBANhIMT0+IyJa9SU8AEyaWZZmT2KEYrjakuadOvlkn3vFdhpvNpnnXiL+cyWy2oU1Q9MAdCTiOPfXmAQt8zIvP2JC8j6yRTcxJCvBwORDyv/uBtXFxBPEC6MDfzU2gKAaHeeJUWrzRv34qFSaYkYta8canK+PSInylQTjJK9VqmjQ';

$ckey 	= 'eJUWrzRv34qFSaYk';
$secret = 'Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GC';

include('/var/lib/symcon/externalscripts/Crypt/Crypt/RSA.php');
include('/var/lib/symcon/externalscripts/Crypt/File/X509.php');

$username	= 'iets@niets.com'; 
$password	= 'eenWW';

$meta['country']		= 'nl';
$meta['lang']			= 'en';
$meta['deviceId']		= md5('MartijnD');; //device_id,
$meta['appCode']		= 'i_eco_e';
$meta['appVersion']		= '1.3.5';
$meta['channel']		= 'c_googleplay';
$meta['deviceType']		= '1';
$meta['account']		= encrypt($username);
$meta['password']		= encrypt($password);
$meta['requestId']		= $meta['deviceId'];
$meta['authTimespan']	= round(microtime(true)*1000);
$meta['authTimeZone']	= 'GMT-8';

$authSign = sign($meta);


// [debug]
echo 'Python: eJUWrzRv34qFSaYkaccount=cW5ilB/p59GQvP0BBQtri0GxAg9X3dphBYPeQwaG7CLbZ9hr7pj+b+90F2UGeol+b3g12M0e3fFvgXMmsKbmzre3lpU9RkfPzy71Nh6dMkr9Sd0GmWfMXOUWZPsuSZXoTbKS+GgNbmxXEmovHjHP36lCkQ5zRPC+X6ncZV3ijNI=appCode=i_eco_eappVersion=1.3.5authTimeZone=GMT-8authTimespan=1533632529513channel=c_googleplaycountry=nldeviceId=d252b60aae03e4123236560e644a2868deviceType=1lang=enpassword=YFC50Ooo/IRFUzKxyuchtitM/GyWXtBUosoka+s1rvFqTTYTtRunjrwD9uCPJKOhxclA2b4ojn7AqfNaGchjVMTwqcLZOK7sCzGkSzQ605Oi3/Mhc5Eb9OcwFiPMnuE5x610AzeTBhtDiOuxP1FcRRndozG4XtQE6tYv9QtGa0k=requestId=35d39fec6bb359e4cab2225ca3185d8dCyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GC
';
echo 'PHP:    '.$authSign.'
';
// [end debug]

$login['account']		= $meta['account'];
$login['password']		= $meta['password'];
$login['requestId']		= $meta['requestId'];
$login['authTimespan']	= $meta['authTimespan'];
$login['authTimeZone']	= $meta['authTimeZone'];
$login['authAppkey']	= $ckey;
$login['authSign']		= md5($authSign);

$function['login']	= 'user/login';

$MAIN_URL_FORMAT = 'https://eco-'.$meta['country'].'-api.ecovacs.com/v1/private/'.$meta['country'].'/'.$meta['lang'].'/'.$meta['deviceId'].'/'.$meta['appCode'].'/'.$meta['appVersion'].'/'.$meta['channel'].'/'.$meta['deviceType'];
$USER_URL_FORMAT = 'https://users-{continent}.ecouser.net:8000/user.do';

$query = "?".http_build_query($login, '', '&');

$url_Login = $MAIN_URL_FORMAT.'/'.$function['login'].$query;

$response = file_get_contents($url_Login);

// [debug]
$url_Python = 'https://eco-nl-api.ecovacs.com/v1/private/nl/en/c670e6fc9d4753af09788e7aaa9502de/i_eco_e/1.3.5/c_googleplay/1/user/login?account=fjPqxztluo6UhPHwo87OVynFNly0eHc%2Bf4VC6QGcWelo9kS%2BMN1m4%2F13XckxEuuPzGoI5MYiubIaWhnsRbpzHqGUD0%2FYkVP%2F4WT2jhsBXZ4tlMxX%2BlMKE25YncaQHRSs80aWk0sPNNKAZm5l%2FEWyI5Z3F1i0L9yNZYZ3rsXdTjA%3D&password=tOSANVu3cq1JXdEHr%2FJqjRGoj03X1Sm9o%2FiM%2FpxZkBMqSSgXa45Sqcs%2BUEmjJOEHJmLmbcxxeoUaFaiiSYZnQc%2FYss0yegbXuO9pzoFBEZPmaPcxcdjAfX8okDeuhZcqzGpL3C2pftH3P8lUTGiQaBCPTIYePx%2Bp3%2BLTPn3MTpU%3D&requestId=035ab98309f011dca9affc7aecad68ed&authTimespan=1533903118436&authTimeZone=GMT-8&authAppkey=eJUWrzRv34qFSaYk&authSign=7455460a324a0d2bf159c2f16c10f698';

echo 'Python: '.url4compare($url_Python);
echo '
';
echo 'PHP:    '.url4compare($url_Login);
echo '
';

print_r($response);

$returnLogin = json_decode($response,true);

echo '
';

echo 'MSG nr.:'.showMsg($returnLogin['code']);
// [end debug]


// Functions:

function encrypt($plaintext) {
	global $key;
	
	//$key = base64_decode($key);
	//$key = bin2hex($key);
	
	$x509 = new File_X509();
	$x509->loadX509($key);
	$pkey = $x509->getPublicKey();
	
	$rsa = new Crypt_RSA();
	$rsa->loadKey($pkey);
	$result = $rsa->encrypt($plaintext);
	//$result = hex2bin($result);
	$result = base64_encode($result);

	return $result;
} // end of function encrypt

function sign($metas) {
	global $ckey, $secret;

	ksort($metas);
	
	$string = '';
	
	foreach($metas as $key => $value) {
		$string = $string.$key.'='.$value;
	}
	return $ckey.$string.$secret;
}


// Below only functions for testing:

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
	$code['1005'] = 'wrong username/password';
	
	return $nr.': '.$code[$nr];
}

		
?>
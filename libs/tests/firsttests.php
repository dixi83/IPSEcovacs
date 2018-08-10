<?php

$key = 'MIIB/TCCAWYCCQDJ7TMYJFzqYDANBgkqhkiG9w0BAQUFADBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMCAXDTE3MDUwOTA1MTkxMFoYDzIxMTcwNDE1MDUxOTEwWjBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDb8V0OYUGP3Fs63E1gJzJh+7iqeymjFUKJUqSD60nhWReZ+Fg3tZvKKqgNcgl7EGXp1yNifJKUNC/SedFG1IJRh5hBeDMGq0m0RQYDpf9l0umqYURpJ5fmfvH/gjfHe3Eg/NTLm7QEa0a0Il2t3Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBANhIMT0+IyJa9SU8AEyaWZZmT2KEYrjakuadOvlkn3vFdhpvNpnnXiL+cyWy2oU1Q9MAdCTiOPfXmAQt8zIvP2JC8j6yRTcxJCvBwORDyv/uBtXFxBPEC6MDfzU2gKAaHeeJUWrzRv34qFSaYkYta8canK+PSInylQTjJK9VqmjQ';

$ckey 	= "eJUWrzRv34qFSaYk";
$secret = "Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GC";

include('/var/lib/symcon/externalscripts/Crypt/Crypt/RSA.php');
include('/var/lib/symcon/externalscripts/Crypt/File/X509.php');

$meta['country']	= 'nl';
$meta['lang']		= 'en';
$meta['deviceId']	= md5(time()); //device_id,
$meta['appCode']	= 'i_eco_e';
$meta['appVersion']	= '1.3.5';
$meta['channel']	= 'c_googleplay';
$meta['deviceType']	= '1';

$authSign = sign($meta);
//echo $authSign;

$username	= 'some@thing.com'; 
$password	= md5('SomePW');

$login['account']	= encrypt($username);
$login['password']	= encrypt($password);
$login['requestId']	= md5(time());
$login['authTimespan']	= time() * 1000;
$login['authTimeZone']	= 'GMT-8';
$login['authAppkey']	= 'eJUWrzRv34qFSaYk';
$login['authSign']	= md5($authSign);

//print_r($login);

$function['login']	= 'user/login';

$MAIN_URL_FORMAT = 'https://eco-'.$meta['country'].'-api.ecovacs.com/v1/private/'.$meta['country'].'/'.$meta['lang'].'/'.$meta['deviceId'].'/'.$meta['appCode'].'/'.$meta['appVersion'].'/'.$meta['channel'].'/'.$meta['deviceType'];
$USER_URL_FORMAT = 'https://users-{continent}.ecouser.net:8000/user.do';

$query = "?".http_build_query($login);

$url_Login = $MAIN_URL_FORMAT.'/'.$function['login'].$query;

echo $url_Login;

$response = file_get_contents($url_Login);//,false,$stream);

echo "
"; // newline

print_r($response);

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
} // end of function encryptRSA

function sign($metas) {
	global $ckey;
	global $secret;

	ksort($metas);
	
	$string = '';
	
	foreach($metas as $key => $value) {
		$string = $string.$key.'='.$value;
	}
	return $ckey.$string.$secret;
}
		
?>

<?php

	/* 
		You need to run this on a HTTP server with PHP and Soap enabled, 
		Apache + PHP (Google XAMPP for a quick easy server setup)
	*/

	ini_set('soap.wsdl_cache_enabled', 0);
	ini_set('soap.wsdl_cache_ttl', 900);
	ini_set('default_socket_timeout', 15);

	$wsdl_url = 'http://localhost:8080/services/AdministrationService?wsdl';
	$base_url = 'http://localhost:8080/';
	
	$client = new SoapClient($wsdl_url);

	/* 
		user to login; can be passed as parameters, fetched from cookies etc.
	*/
	$userNameToLogin = 'user@yellowfin.com.au';
	$userPasswordToLogin = 'test';

	/* 
		Yellowfin user account with rights to perform web services calls:
	*/
	$webserviceAdmin = 'admin@yellowfin.com.au';
	$webserviceAdminPassword = 'test';

	$sessionId = loginUser($userNameToLogin,$userPasswordToLogin);

	if ($sessionId!=null) {
		
		$url = $base_url.'logon.i4?LoginWebserviceId='.$sessionId;
		
		echo "<a href='".$url."' target='_blank'>Click to login</a>"; //Print out url
		//header('Location: '.$url);	// uncomment to redirect
		
	} else echo "<br>Login Failed...<br>";


	
/* 
	performing LOGINUSER call to get logon token
*/

function loginUser($userName,$userPassword){
	
	$userToLogin['userId']=$userName;
    $userToLogin['password']=$userPassword;
	
	$AdministrationServiceRequest['function']='LOGINUSER';
    $AdministrationServiceRequest['person']= $userToLogin;
	$AdministrationServiceRequest['loginId']= $GLOBALS['webserviceAdmin'];
    $AdministrationServiceRequest['password']= $GLOBALS['webserviceAdminPassword'];
    $AdministrationServiceRequest['orgId']=1;
    $AdministrationServiceRequest['ntlm']=false;
	
	$response = doWebserviceCall($AdministrationServiceRequest);
	/*
	echo "<br>Response:<br>";
    var_dump($response);
	echo "<br><hr>";
	*/
	if ($response!=null and strcmp($response->statusCode,'SUCCESS')==0) return $response->loginSessionId;
	
	return null;
	
}


/* 
	sending the request to Yellowfin server
*/

function doWebserviceCall($rsr){

    try {
        $rs = $GLOBALS['client']->remoteAdministrationCall($rsr);
    }
    catch (Exception $e)
    {
		echo "Error! <br>";
		echo $e -> getMessage();
		echo 'Last response: '. $GLOBALS['client']->__getLastResponse();
		return null;
    }
	return $rs;
}



/*
	cerating new Yellowfin user account
*/

function addUser($userName,$userPassword,$userEmail,$userLastName,$userFirstName,$userRoleCode){
	
	$user['userId']=$userName;
    $user['password']=$userPassword;
	$user['emailAddress']=$userEmail;
	$user['lastName']=$userLastName;
	$user['firstName']=$userFirstName;
	$user['roleCode']=$userRoleCode;
	
	$AdministrationServiceRequest['function']='ADDUSER';
    $AdministrationServiceRequest['person']= $user;
	$AdministrationServiceRequest['loginId']= $GLOBALS['webserviceAdmin'];
    $AdministrationServiceRequest['password']= $GLOBALS['webserviceAdminPassword'];
    $AdministrationServiceRequest['orgId']=1;
    $AdministrationServiceRequest['ntlm']=false;
	
	$response = doWebserviceCall($AdministrationServiceRequest);
	
	if ($response!=null and strcmp($response->statusCode,'SUCCESS')==0) return true;
	
	return false;
	
}

?>



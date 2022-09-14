<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

	require_once __DIR__ . '/contact/Contact.php';
	require_once __DIR__ . '/contact/ContactAPI.php';

	$payload = getRequestInfo();
	
    //connection to database
	$servername = "127.0.0.1";
    $username = "root";
    $password = "MyPassword";
    $dbname = "COP4331";

    // Create connection
    $mysql = new mysqli($servername, $username, $password, $dbname);

	if ($payload == null)
		returnWithError("Payload is empty");
	else {
		$contact = Contact::Deserialize($payload);

		if ($mysql->connect_error != null) 
			returnWithError( $mysql->connect_error );
		else
		{
			$contactAPI = new ContactAPI($mysql);

			$result = $contactAPI->CreateContact($contact);

			if ($result == false)
				returnWithError("Couldn't create a contact");
			else
				sendResultInfoAsJson($result->getJSON());
		}

	}
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>

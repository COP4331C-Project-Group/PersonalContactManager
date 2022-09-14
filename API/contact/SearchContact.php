<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

	require_once __DIR__ . '/Contact.php';
	require_once __DIR__ . '/ContactAPI.php';

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
	else
	{
		$query = $payload['query'];

		if ($mysql->connect_error != null)
			returnWithError($mysql->connect_error);
		else
		{
			$contactAPI = new ContactAPI($mysql);

			$result = $contactAPI->GetContact($query, 10);

			if ($result == false)
				returnWithError("Couldn't find contact");
			else
			{
				$resultSerialized = "[";

				foreach ($result as $contact)
					$resultSerialized = $resultSerialized . $contact->getJSON() . ",";

				$resultSerialized = substr($resultSerialized, 0, strlen($resultSerialized) - 1);
				$resultSerialized = $resultSerialized . "]";
				
				sendResultInfoAsJson($resultSerialized);
			}
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

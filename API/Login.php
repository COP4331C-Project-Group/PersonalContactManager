<?php

	ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

	$inData = getRequestInfo();

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "myDB";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		$userExists = userExists($conn, $inData['username']);

		if ($userExists === false){
			flush();
			returnWithError("User doesn't exist.");
			return;
		}

		$checkPass = password_verify($inData['password'], $userExists['password']);

		if ($checkPass === false){
			flush();
			returnWithError("Incorrect password.");
			return;
		}

		// returnWithInfo( $userExists['firstName'], $userExists['lastName'], $userExists['ID'] );
		returnWithError("");

		// $stmt->close();
		$conn->close();
	}

	function userExists($conn, $username) {
		$stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?;");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($row = $result->fetch_assoc()) {
			return $row;
		}
		else {
			$result = false;
			return $result;
		}

		$stmt->close();
	}
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		// header('Content-type:application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"userID":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"userID":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>

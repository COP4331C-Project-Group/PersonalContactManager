<?php

    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

	$inData = getRequestInfo();

	$servername = "localhost";
	$sv_username = "root";
	$sv_password = "";
	$dbname = "myDB";

	// Create connection
	$conn = new mysqli($servername, $sv_username, $sv_password, $dbname); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		if (userExists($conn, $inData['username']) != false)
		{
			returnWithError("Username Already Taken");
		}
		else
		{
			$hashed_password = password_hash($inData["password"], PASSWORD_DEFAULT);
			
			$stmt = $conn->prepare("INSERT INTO Users (ID, firstName, lastName, username, password, dateCreated) VALUES (DEFAULT, ?, ?, ?, ?, DEFAULT)");
			$stmt->bind_param("ssss", $inData["firstName"], $inData["lastName"], $inData["username"], $hashed_password);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			returnWithInfo($row['firstName'], $row['lastName'], $row['ID']);
		}

		$stmt->close();
		$conn->close();
	}

	function userExists($conn, $username) {
		$stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?;");
		$stmt->bind_param("s", $inData['username']);
		$stmt->execute();
		$result = get_result($stmt);

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
		header('Content-type: application/json');
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

<?php

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
		if userExists($conn, $inData['userName'])
		{
			returnWithError("Username Already Taken")
		}
		else
		{
			$hashed_password = password_hash($inData["password"], PASSWORD_DEFAULT);
			
			$stmt = $conn->prepare("INSERT INTO Users (ID, firstName, lastName, userName, password, dateCreated) VALUES (NULL, ?, ?, ?, ?, NULL)");
			$stmt->bind_param("ssss", $inData["firstName"], $inData["lastName"], $inData["userName"], $inData["password"]);
			$stmt->execute();
			$result = $stmt->get_result();
		}

		$stmt->close();
		$conn->close();
	}

	function userExists($conn, $username) {
		$stmt = $conn->prepare("SELECT * FROM Users WHERE userName = ?;");
		$stmt->bind_param("s", $inData['userName']);
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

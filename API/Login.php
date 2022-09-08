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
		$userExists = userExists($conn, $inData['userName']);

		if ($userExists === false)
			returnWithError("User doesn't exist.")

		$checkPass = password_verify($inData['password'], $userExists['password']);

		if ($checkPass === false)
			returnWithError("Incorrect password.");

		returnWithInfo( $userExists['firstName'], $userExists['lastName'], $userExists['userID'] );

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
		header('Content-type:application/json');
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

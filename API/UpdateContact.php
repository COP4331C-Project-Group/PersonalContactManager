<?php
	$inData = getRequestInfo();
	
	$ID = $inData["ID"];
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $email = $inData["email"];
    $phone = $inData["phone"];
    $userID = $inData["userID"];

    //connection to database
	$servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "myDB";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		//determine which piece of data that we are using to determine which contact to update
		$stmt = $conn->query("UPDATE FROM Contacts SET firstName='$firstName', lastName='$lastName', email='$email', phone='$phone', userID='$userID' WHERE ID='$ID'");
		$stmt->close();
		$conn->close();
		returnWithError("");
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

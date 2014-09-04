<?php
	 
/*
	Our "config.inc.php" file connects to database every time we include or require
	it within a php script.  Since we want this script to add a new user to our db,
	we will be talking with our database, and therefore,
	let's require the connection to happen:
	*/
	require("config.inc.php");
	 
	//HL: query that we are going to execute
	//:user here is the variable that we are going to fill in the value in the next line
	$query = "SELECT * FROM `covers`";
	 
	//HL: executing the query that we defined above
	try {
	    $stmt   = $db->prepare($query);
	    $result = $stmt->execute();
	}
	catch (PDOException $ex) {
	    $response["success"] = 0;
	    $response["message"] = "Database Error!";
	    die(json_encode($response));
	}
	 
	// HL: retrieve the data using fetchall() and populate it into $rows
	$rows = $stmt->fetchAll();
	 
	 
	if ($rows) {
	    $response["success"] = 1;
	    $response["message"] = "Data Available!";
	    $response["data"]   = array();
	     
	    foreach ($rows as $row) {
			//HL: does the variable name "post" matter?
			$data["url"]    = $row["url"];
			$data["id"]    = $row["id"];
			$data["category"]    = $row["category"];
	         
	        //update our repsonse JSON data
	        array_push($response["data"], $data);
	    }
	     
	    // echoing JSON response
	    echo json_encode($response);
	     
	     
	} else {
	    $response["success"] = 0;
	    $response["message"] = "No Post Available!";
	    die(json_encode($response));
	}
	 
?>
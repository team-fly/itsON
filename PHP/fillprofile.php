<?php
	 
/*
	Our "config.inc.php" file connects to database every time we include or require
	it within a php script.  Since we want this script to add a new user to our db,
	we will be talking with our database, and therefore,
	let's require the connection to happen:
	*/
	
if (!empty($_POST)) {

	require("config.inc.php");
	
	$user_id=$_POST['user_id'];

	//////////////////////////////////////////////////////////////////
	   
	//////////		RETRIEVES URL FOR THE USER DP	 	//////////////
	  
	//////////////////////////////////////////////////////////////////
	
	$query = "SELECT dp_url FROM users WHERE user_id='$user_id'";

	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error!";
		die(json_encode($response));
	}
		
	$row = $stmt->fetch();
	$response["dp_url"]=$row["dp_url"];
	
	
	//////////////////////////////////////////////////////////////////////
	   
	//////////		RETRIEVES URL FOR THE USER COVER	 	//////////////
	  
	//////////////////////////////////////////////////////////////////////
	
	$query = "SELECT cover_url FROM users WHERE user_id='$user_id'";

	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error!";
		die(json_encode($response));
	}
		
	$row = $stmt->fetch();
	$response["cover_url"]=$row["cover_url"];

	
	//////////////////////////////////////////////////////////////////////////
	   
	//////////		CHECKS IF CHALLENGE REQUEST AVAILABLE	 	//////////////
	  
	//////////////////////////////////////////////////////////////////////////

	$query = "SELECT * FROM `challenges_main` WHERE status ='0' AND user_2='$user_id'";

	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error!";
		die(json_encode($response));
	}
	
	$rows = $stmt->fetch();
	
	if($rows){	
		$response["is_challenge_request_available"]=1;
	}else{
		$response["is_challenge_request_available"]=0;
	}

	$response["success"] = 1;	
	
	$date=date('Y-m-d'); 
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	   
	//////////		SETS CHALLENGE TO COMPLETE IF END DATE MATCHES TODAY	 	//////////////
	  
	//////////////////////////////////////////////////////////////////////////////////////////

	
	$query = "UPDATE challenges_main
		SET iscomplete='1'
		WHERE (user_1 ='$user_id' OR user_2 ='$user_id')
		AND iscomplete='0'
		AND '$date'> end_date";

	try {
		$stmt   = $db->prepare($query);
		$stmt->execute(); 
		$numRowsAffected=$stmt->rowCount();
	
		$response["archived"] = $numRowsAffected;
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Error Updating Table";
		die(json_encode($response)); 
	}
			

	
	
	$query = "SELECT challenges_main.id, challenges_main.name, challenges_main.description, covers.url  AS 'url'
				FROM `challenges_main`
				INNER JOIN `covers`
				ON challenges_main.image_id=covers.id
				WHERE (challenges_main.user_1 ='$user_id' OR challenges_main.user_2 ='$user_id')
				AND challenges_main.status='1'
				AND challenges_main.iscomplete='0'";
	

	try {
	    $stmt   = $db->prepare($query);
	    $stmt->execute();
	}
	catch (PDOException $ex) {
	    $response["success"] = 0;
	    $response["message"] = "Database Error!";
	    die(json_encode($response));
	}
	 

	$rows = $stmt->fetchAll();
	 
	 
	if ($rows) {

		$response["is_challenge_available"]=1;
	    $response["challengeinfo"]   = array();
	     
	    foreach ($rows as $row) {
			//HL: does the variable name "post" matter?
			
	        $post = array();
			$post["id"]    = $row["id"]; //check if this becomes an integer
	        $post["name"] = $row["name"];
	        $post["description"]    = $row["description"];
			$post["url"]=$row["url"];
		
		
			
			$challenge_id=$row["id"];
			
			//Get the info from udates table to see if challenge has been completed for today
			$query="SELECT iscomplete AS 'iscomplete'
				FROM updates
				WHERE challenge_id = '$challenge_id'
				AND updates.date = '$date'
				AND updates.user_id = '$user_id'";
			
				
			try {
				$stmt   = $db->prepare($query);
				$stmt->execute();
			}
			catch (PDOException $ex) {
				$response["success"] = 0;
				$response["message"] = "Database Error!";
				die(json_encode($response));
			}
			
			$row = $stmt->fetch();
			$post["iscomplete"]=$row["iscomplete"];
		
			//check if we have to array push everytime
	        array_push($response["challengeinfo"], $post);
	    }
	     
	    // echoing JSON response
	    echo json_encode($response);
	     
	     
	} else {
		$response["is_challenge_available"]=0;
	    $response["message"] = "No Challenges!";
	    echo json_encode($response);
	}
	 
} else {
?>
    <h1>Fill Profile</h1>
    <form action="fillprofile.php" method="post">
	User ID:<br />
        	<input type="text" name="user_id" value="" />
        <br /><br />
        <input type="submit" value="Add" />
	 </form>
<?php
	}
?>
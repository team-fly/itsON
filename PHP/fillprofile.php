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
	
	
			
	///////////////////////////////////////////////////////////////////////////////////
	   
	//////////		RETRIEVES ALL CHALLENGES THAT AREN'T COMPLETE	 	//////////////
	  
	///////////////////////////////////////////////////////////////////////////////////
	
	
	$query = "SELECT challenges_main.*, covers.url AS 'cover_url', 
						A.username AS 'user_1_username', A.dp_url AS 'user_1_dp_url',
						B.username AS 'user_2_username', B.dp_url AS 'user_2_dp_url'
				FROM `challenges_main`
                INNER JOIN users A ON A.user_id=challenges_main.user_1
                INNER JOIN users B ON B.user_id=challenges_main.user_2
				INNER JOIN covers ON challenges_main.image_id=covers.id
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
	 
	$response["success"] = 1;	
	$response["message"] = "Challenges Retrieved";
		
	$rows = $stmt->fetchAll();
	 
	 
	if ($rows) {

		$response["is_challenge_available"]=1; //TODO: remove later
	    
		$response["challenge_info"]=array();
		
	    foreach ($rows as $row) {
	        $post = array();
			
			$challenge_id=$row["id"]; 
			$user_1=$row["user_1"]; 
			$user_2=$row["user_2"]; 
			
			$post["id"]    = $row["id"]; 
	        $post["name"] = $row["name"];
	        $post["description"]    = $row["description"];
			$post["category"]=$row["category"];
			$post["start_date"]=$row["start_date"];
			$post["end_date"]=$row["end_date"];
			$post["user_1"]=$row["user_1"];
			$post["user_2"]=$row["user_2"];
			$post["user_1_dp_url"]=$row["user_1_dp_url"];
			$post["user_2_dp_url"]=$row["user_2_dp_url"];
			$post["user_1_username"]=$row["user_1_username"];
			$post["user_2_username"]=$row["user_2_username"];
			$post["days_of_week"]=$row["days_of_week"];
			$post["cover_url"]=$row["cover_url"];
			
			$query="SELECT * FROM updates 
					WHERE challenge_id='$challenge_id'
					AND user_id='$user_id'
					AND date='$date'";
					
			try {
				$stmt   = $db->prepare($query);
				$stmt->execute();
			}catch (PDOException $ex) {
				$response["success"] = 0;
				$response["message"] = "Database Error!";
				die(json_encode($response));
			}
			
			$rows = $stmt->fetchAll();
			
			if($rows){
				$post["update_status"]=1;
			}else{
				$post["update_status"]=0;
			}
			
			array_push($response["challenge_info"], $post);
		}
	
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
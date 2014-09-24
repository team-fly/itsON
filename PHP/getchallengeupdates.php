<?php
	 
require("config.inc.php");
	
if (!empty($_POST)){

	$challenge_id= $_POST['challenge_id'];
	$todaydate=date('Y-m-d'); 
	
	////////////////////////////////////////////////////////////////////////////////////////
	   
	/////////////		RETRIEVES START DATE, END DATE, SELECTED WEEKDAYS	 	////////////
	  
	////////////////////////////////////////////////////////////////////////////////////////
	
	$query="SELECT user_1 AS 'user_1', user_2 AS 'user_2' 
			FROM challenges_main
			WHERE id='$challenge_id'";
	
	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error 1!";
		die(json_encode($response));
	}
	
	$row = $stmt->fetch();
	
	$user_1=$row["user_1"];
	$user_2=$row["user_2"];
	
	//////////////////////////////////////////////////////////////////////////
	   
	/////////////		RETRIEVES USER 1 INFORMATION DATA	 	//////////////
	  
	//////////////////////////////////////////////////////////////////////////
	
	//populating dates
	$query = "SELECT date AS 'date', message AS 'message' 
		FROM updates
		WHERE user_id='$user_1'
		AND challenge_id='$challenge_id'" ;

	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error 2!";
		die(json_encode($response));
	}
	
	$rows = $stmt->fetchAll();

	$response["user_1_updates"]=array();
	
	foreach ($rows as $row) {
			
		$post = array();
		$post["date"]    = $row["date"]; 
		$post["message"] = $row["message"];
		
		array_push($response["user_1_updates"], $post);
	}
	
	//////////////////////////////////////////////////////////////////////////
	   
	/////////////		RETRIEVES USER 2 INFORMATION DATA	 	//////////////
	  
	//////////////////////////////////////////////////////////////////////////

	//populating dates
	$query = "SELECT date AS 'date', message AS 'message' 
		FROM updates
		WHERE user_id='$user_2'
		AND challenge_id='$challenge_id'" ;

	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error 3!";
		die(json_encode($response));
	}
	
	$rows = $stmt->fetchAll();

	$response["user_2_updates"]=array();
	
	foreach ($rows as $row) {
			
		$post = array();
		$post["date"]    = $row["date"]; 
		$post["message"] = $row["message"];
		
		array_push($response["user_2_updates"], $post);
	}
	
	
	//////////////////////////////////////////////////////////////////////////
	   
	/////////////		RETRIEVES USER 2 INFORMATION DATA	 	//////////////
	  
	//////////////////////////////////////////////////////////////////////////
	 
	$response["success"] = 1;
	$response["message"] = "Success";
	
	echo json_encode($response); 
	 
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Fill Challenge</h1>
        <form action="getchallengeupdates.php" method="post">
        Challenge ID:<br />
         	<input type="text" name="challenge_id" value=""/>
        <br /><br />
        
        <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 

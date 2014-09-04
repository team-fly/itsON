<?php

require("config.inc.php");
 
if (!empty($_POST)) {
	
	$user_id=$_POST['user_id'];
	$friend_id=$_POST['friend_id'];

	$query = "DELETE FROM `friends` 
				WHERE (friend_one='$user_id' AND friend_two='$friend_id')
				OR (friend_one='$friend_id' AND friend_two='$user_id')";
	
	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error 1";
		die(json_encode($response));
	}
	 
	$query = "SELECT id FROM `challenges_main`
						WHERE (user_1='$user_id' AND user_2='$friend_id')
						OR (user_1='$friend_id' AND user_2='$user_id')";
	
	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error 2";
		die(json_encode($response));
	}
	
	$challenges = $stmt->fetchAll();
	if($challenges){
		foreach ($challenges as $challenge) {
			echo $challenge["id"];
			//HL: does the variable name "post" matter?
			$challenge_id=$challenge["id"];
			$query = "DELETE FROM `challenges_main` 
						WHERE (id='$challenge_id' AND iscomplete='0')";
			
			try {
				$stmt   = $db->prepare($query);
				$stmt->execute();
			}
			catch (PDOException $ex) {
				$response["success"] = 0;
				$response["message"] = "Database Error 3";
				die(json_encode($response));
			}
			
			$query = "DELETE FROM `updates` 
					WHERE challenge_id='$challenge_id'";
			
			try {
				$stmt   = $db->prepare($query);
				$stmt->execute();
			}
			catch (PDOException $ex) {
				$response["success"] = 0;
				$response["message"] = "Database Error 4";
				die(json_encode($response));
			}
	    }
	}
	
	$response["success"] = 1;
	$response["message"] = "Friend Deleted";
	die(json_encode($response));	
	 
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Delete Friend</h1>
        <form action="deletefriend.php" method="post">
        User Id:<br />
            <input type="text" name="user_id" value=""/>
            <br />
		Friend Id:<br />
            <input type="text" name="friend_id" value=""/>
            <br /><br />
            <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 
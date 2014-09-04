<?php
	 
if (!empty($_POST)) {

	require("config.inc.php");
	
	$challenge_id=$_POST['challenge_id'];

	$query = "DELETE FROM `challenges_main` 
				WHERE id='$challenge_id'";
	
	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error!";
		die(json_encode($response));
	}
	 
	$query = "DELETE FROM `updates` WHERE challenge_id='$challenge_id'";
	
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
	$response["message"] = "Challenge Deleted";
	die(json_encode($response));
						
	 
} else {
?>
    <h1>Delete Challenge</h1>
    <form action="deletechallenge.php" method="post">
	User ID:<br />
			<input type="text" name="challenge_id" value="" />
        <br />
	
		<br />
        <input type="submit" value="Add" />
	 </form>
<?php
	}
?>
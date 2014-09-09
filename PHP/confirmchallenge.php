<?php
	 
//load and connect to MySQL database stuff
require("config.inc.php");

if (!empty($_POST)) {
    //gets user's info based off of a username.
	
	$challenge_id = $_POST['challenge_id'];
	
    $query = "UPDATE challenges_main
		SET status='1'
		WHERE (id='$challenge_id')";
     
    try {
        $stmt   = $db->prepare($query); //HL: prepares the query. what is $db, $stmt
        $stmt->execute();  //executes the query
		$response["success"] = 1;
		$response["message"] = "Challenge Accepted";
		die(json_encode($response));
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Unable to Accept Challeng";
        die(json_encode($response));  //what does the die do here? turns off the script?
    }
	
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Confirm Challenge</h1>
        <form action="confirmchallenge.php" method="post">
        Challenge Id:<br />
            <input type="text" name="user_id" value=""/>
            <br /><br />
			<input type="submit" value="Accept" />
        </form>
    <?php
} 
?> 
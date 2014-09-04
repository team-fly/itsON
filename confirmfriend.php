<?php
	 
//load and connect to MySQL database stuff
require("config.inc.php");

if (!empty($_POST)) {
    //gets user's info based off of a username.
	
	$user_id = $_POST['user_id'];
	$friend_id = $_POST['friend_id'];
	
    $query = "UPDATE friends
		SET status='1'
		WHERE (friend_one='$user_id' OR friend_two='$user_id')
		AND (friend_one='$friend_id' OR friend_two='$friend_id')";
     
    try {
        $stmt   = $db->prepare($query); //HL: prepares the query. what is $db, $stmt
        $stmt->execute();  //executes the query
		$response["success"] = 1;
		$response["message"] = "Friend Confirmed!";
		die(json_encode($response));
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Unable to confirm friend";
        die(json_encode($response));  //what does the die do here? turns off the script?
    }
	
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Confirm Friend</h1>
        <form action="confirmfriend.php" method="post">
        User Id:<br />
            <input type="text" name="user_id" value=""/>
            <br /><br />
		Friend Id:<br />
            <input type="text" name="friend_id" value=""/>
            <br /><br />
            <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 
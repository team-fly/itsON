<?php
	 
//load and connect to MySQL database stuff
require("config.inc.php");

if (!empty($_POST)) {

    //gets user's info based off of a username.
	
	$user_id=$_POST['user_id'];
	$friend=$_POST['friend'];
	
	
    $query = "SELECT user_id AS 'id' FROM users 
				WHERE username ='$friend'
				OR emailaddr='$friend'";
     
    try {
        $stmt   = $db->prepare($query); //HL: prepares the query. what is $db, $stmt
		$stmt->execute();  //executes the query
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Error Accessing User Database";
        die(json_encode($response));  //what does the die do here? turns off the script?
         
    }

	$data=$stmt->fetch();
	
	$friend_id;
	if ($data){
		$friend_id=$data["id"];
	} else {
	    $response["success"] = 0;
	    $response["message"] = "User not found in system";
	    die(json_encode($response));
	}
	
	$query="INSERT INTO friends
			(friend_one,friend_two)
			VALUES
			('$user_id','$friend_id')";
			
	try {
        $stmt   = $db->prepare($query); //HL: prepares the query. what is $db, $stmt
		$stmt->execute();  //executes the query
    }
    catch (PDOException $ex) {
        $response["success"] = 0;
        $response["message"] = "Error Processing Friend Request";
        die(json_encode($response));  //what does the die do here? turns off the script?
         
    }
	$response["success"] = 1;
	echo json_encode($response);
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Add Friend</h1>
        <form action="addfriend.php" method="post">
            Friend Username:<br />
            <input type="text" name="friend_username" value=""/>
            <br /><br />
            User Id:<br />
            <input type="text" name="user_id"  value="" />
            <br /><br />
            <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 
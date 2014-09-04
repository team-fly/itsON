<?php
	 
require("config.inc.php");
	
if (!empty($_POST)){

	$user_id=$_POST['user_id'];
	 
	$query = "SELECT users.user_id AS 'friend_id', users.username AS 'friend_username',
				users.dp_url AS 'friend_dp_url',users.emailaddr AS 'friend_email_address'
			FROM users 
			INNER JOIN friends 
			ON users.user_id=friends.friend_one  
			WHERE (friends.friend_two='$user_id')
			AND (friends.status='0')";
	 
	//HL: executing the query that we defined above
	try {
	    $stmt   = $db->prepare($query);
	    $stmt->execute();
	}
	catch (PDOException $ex) {
	    $response["success"] = 0;
	    $response["message"] = "Database Error!";
	    die(json_encode($response));
	}
	$friends = $stmt->fetchAll();
	
	
	//$response["challenges"]   = array();
	
	if ($friends)
	{
		$response["success"] = 1;
		$response["friends"]   = array();
	     
	    foreach ($friends as $friend) {
			
	        $post = array();
			$post["friend_id"]    = $friend["friend_id"]; 
	        $post["friend_username"] = $friend["friend_username"];
			$post["friend_dp_url"] = $friend["friend_dp_url"];
			$post["friend_email_address"] = $friend["friend_email_address"];
			
	        array_push($response["friends"], $post);
	    }
		echo json_encode($response); //this is what is actually sent out. updates the response to android
	}
	else
	{
		$response["success"] = 0;
	    $response["message"] = "Unable to Find Challenge";
	    die(json_encode($response));
	}
	 

	 
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Add Friend</h1>
        <form action="retrievefriendrequests.php" method="post">
		User Id:<br />
            <input type="text" name="user_id" value=""/>
            <br /><br />
         <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 

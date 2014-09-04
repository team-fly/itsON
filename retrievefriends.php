<?php
	 
require("config.inc.php");
	
if (!empty($_POST)){

	$user_id=$_POST['user_id'];
	
	$query = "SELECT * FROM `friends` WHERE status ='0' AND friend_two='$user_id'";

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
		$response["friend_request_available"]=1;
	}else{
		$response["friend_request_available"]=0;
	}
	
	 
	$query = "SELECT users.user_id AS 'friend_id', users.username AS 'friend_username',
				users.dp_url AS 'friend_dp_url',users.emailaddr AS 'friend_email_address'
			FROM friends
			INNER JOIN users ON friends.friend_two = users.user_id
			WHERE (friend_one = '$user_id')
			AND (STATUS = '1')
		UNION ALL
			SELECT users.user_id AS 'friend_id', users.username AS 'friend_username', 
				users.dp_url AS 'friend_dp_url',users.emailaddr AS 'friend_email_address'
			FROM friends
			INNER JOIN users ON friends.friend_one = users.user_id
			WHERE (friend_two = '$user_id')
			AND (STATUS = '1')";
	 
	//HL: executing the query that we defined above
	try {
	    $stmt   = $db->prepare($query);
	    $stmt->execute();
	}
	catch (PDOException $ex) {
	    //$response["success"] = 0;
	    $response["message"] = "Database Error!";
	    die(json_encode($response));
	}
	$friends = $stmt->fetchAll();
	
	$response["success"] = 1;
	
	if ($friends)
	{
		$response["friends"]   = array();
	     
	    foreach ($friends as $friend) {
	        $post = array();
			$post["friend_id"]    = $friend["friend_id"]; 
	        $post["friend_username"] = $friend["friend_username"];
			$post["friend_dp_url"] = $friend["friend_dp_url"];
			$post["friend_email_address"] = $friend["friend_email_address"];
			
	        array_push($response["friends"], $post);
	    }
		echo json_encode($response); 
	}
	else
	{
		$response["success"] = 0;
	    $response["message"] = "Unable to Retrieve Friends";
	    die(json_encode($response));
	}
	 
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Add Friend</h1>
        <form action="retrievefriends.php" method="post">
		User Id:<br />
            <input type="text" name="user_id" value=""/>
            <br /><br />
         <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 

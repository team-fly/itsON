<?php

require("config.inc.php");

if (!empty($_POST)) {

	
	$user_id=$_POST['user_id'];
	$date=date('Y-m-d'); 
	
	$query = "SELECT challenges_main.id AS 'id', challenges_main.name AS 'name', 
				challenges_main.description AS 'description', 
				covers.url  AS 'url',
				users.username AS 'friend_username'
				FROM challenges_main
				INNER JOIN users
					ON challenges_main.user_1=users.user_id
				INNER JOIN covers
					ON challenges_main.image_id=covers.id
				WHERE (challenges_main.user_2 ='$user_id')
				AND challenges_main.status='0'";
	
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

	    $response["challenge_requests"]   = array();
	     
	    foreach ($rows as $row) {
			//HL: does the variable name "post" matter?
			
			$post = array();
			$post["id"]    = $row["id"]; //check if this becomes an integer
			$post["name"] = $row["name"];
			$post["description"] = $row["description"];
			$post["url"]=$row["url"];
			$post["friend_username"]=$row["friend_username"];
			
			array_push($response["challenge_requests"], $post);
	    }
	    
	    $response["success"] = 1;
	    $response["message"] = "Challenge Requests Retrieved";
	     
	    echo json_encode($response);
	     
	} else {
	    $response["success"] = 0;
	    $response["message"] = "Unable to Retrieve Challenges";
	    die(json_encode($response));
	}
	 
} else {
?>
    <h1>Add Challenge</h1>
    <form action="fillchallengerequests.php" method="post">
	User ID:<br />
        	<input type="text" name="user_id" value="" />
        <br /><br />
        <input type="submit" value="Add" />
    </form>
<?php
	}
?>
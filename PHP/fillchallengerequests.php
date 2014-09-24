<?php

require("config.inc.php");

if (!empty($_POST)) {

	$user_id=$_POST['user_id'];
	$date=date('Y-m-d'); 
	
	$query = "SELECT challenges_main.*, covers.url AS 'cover_url', 
						A.username AS 'user_1_username', A.dp_url AS 'user_1_dp_url',
						B.username AS 'user_2_username', B.dp_url AS 'user_2_dp_url'
				FROM `challenges_main`
                INNER JOIN users A ON A.user_id=challenges_main.user_1
                INNER JOIN users B ON B.user_id=challenges_main.user_2
				INNER JOIN covers ON challenges_main.image_id=covers.id
				WHERE  challenges_main.user_2 ='$user_id'
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
			
			$post["update_status"]=0;
			
			array_push($response["challenge_info"], $post);
		}
	
	    echo json_encode($response);
	} else {
		$response["is_challenge_available"]=0;
	    $response["message"] = "No Challenges!";
	    echo json_encode($response);
		
	}
	
	$response["success"] = 1;
	$response["message"] = "Challenge Requests Retrieved";
	 
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
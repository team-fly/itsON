<?php
	 
require("config.inc.php");
	
if (!empty($_POST)){

	$user_id=$_POST['user_id'];
	$challenge_id= $_POST['challenge_id'];
	$todaydate=date('Y-m-d'); 

	//query to grab all the user information for both users wrt to the specific day
	$query="SELECT * FROM
			(SELECT updates.iscomplete AS 'user_1_iscomplete' , updates.message AS 'user_1_message',
					updates.challenge_id AS 'challenge_id',
					users.username AS 'user_1_username',users.dp_url AS 'user_1_dpurl'
							FROM updates 
											INNER JOIN users
											ON updates.user_id=users.user_id
							WHERE date='$todaydate' 
							AND updates.challenge_id='$challenge_id' 
							AND updates.user_id='$user_id') AS A
			JOIN 
			(SELECT updates.iscomplete AS 'user_2_iscomplete', updates.message AS 'user_2_message',
					updates.challenge_id AS 'challenge_id',
					users.username AS 'user_2_username', users.dp_url AS 'user_2_dpurl'
							FROM users
							INNER JOIN updates ON users.user_id = updates.user_id
							WHERE updates.challenge_id = '$challenge_id'
							AND updates.date = '$todaydate'
							AND updates.user_id <> '$user_id') AS B
                                
			ON A.challenge_id=B.challenge_id";
	
	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Database Error!";
		die(json_encode($response));
	}
	
	$row = $stmt->fetch();
	
	if($row){
	
		$response["challenge_info"]   = array();
		
		$post = array();
		$post["user_1_iscomplete"]    = $row["user_1_iscomplete"]; 
		$post["user_1_username"] = $row["user_1_username"];
		$post["user_1_message"]    = $row["user_1_message"];
		$post["user_1_dpurl"]    = $row["user_1_dpurl"];
		
		$post["user_2_iscomplete"]    = $row["user_2_iscomplete"]; 
		$post["user_2_username"] = $row["user_2_username"];
		$post["user_2_message"]    = $row["user_2_message"];
		$post["user_2_dpurl"]    = $row["user_2_dpurl"];
		
		
		array_push($response["challenge_info"], $post);
	}
	
    
	
	
	$query = "SELECT * FROM updates
		WHERE user_id='$user_id'
		AND challenge_id='$challenge_id'
		ORDER BY date ASC" ;
		
	try {
	    $stmt   = $db->prepare($query);
	    $stmt->execute();
	}
	catch (PDOException $ex) {
	    //$response["success"] = 0;
	    $response["message"] = "Database Error!";
	    die(json_encode($response));
	}
	
	$response["success"] = 1;
	$response["message"] = "Success";
	$dates = $stmt->fetchAll();
	
	
	
	if ($dates)
	{
		$response["dates"]   = array();
		     
		foreach ($dates as $date) {
				
			$post = array();
			$post["date"]    = $date["date"]; 
			$post["iscomplete"] = $date["iscomplete"];
		    $post["messages"]    = $date["message"];
				
		    array_push($response["dates"], $post);
		}
		
		
		echo json_encode($response); 
	
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
        <h1>Fill Challenge</h1>
        <form action="fillchallenge.php" method="post">
	User ID:<br />
         	<input type="text" name="user_id" value=""/>
        <br /><br />
        
        Challenge ID:<br />
         	<input type="text" name="challenge_id" value=""/>
        <br /><br />
        
        <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 

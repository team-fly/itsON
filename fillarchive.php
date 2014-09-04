<?php

require("config.inc.php");

if (!empty($_POST)) {

	
	$user_id=$_POST['user_id'];
	
	$query = "SELECT * FROM challenges_main
				WHERE (user_1='$user_id' OR user_2='$user_id')
				AND iscomplete='1'";
	
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

	    $response["archived_challenges"]   = array();
	     
	    foreach ($rows as $row) {
			//HL: does the variable name "post" matter?
			
			$post = array();
			$post["id"]    = $row["id"]; //check if this becomes an integer
			$post["name"] = $row["name"];
			$post["description"] = $row["description"];
			$post["end_date"] = $row["end_date"];
		
			array_push($response["archived_challenges"], $post);
	    }
	    
	    $response["success"] = 1;
	    $response["message"] = "Post Available!";
	     
	    echo json_encode($response);
	     
	} else {
	    $response["success"] = 0;
	    $response["message"] = "No Post Available!";
	    die(json_encode($response));
	}
	 
} else {
?>
    <h1>Add Challenge</h1>
    <form action="fillarchive.php" method="post">
	User ID:<br />
        	<input type="text" name="user_id" value="" />
        <br /><br />
        <input type="submit" value="Add" />
    </form>
<?php
	}
?>
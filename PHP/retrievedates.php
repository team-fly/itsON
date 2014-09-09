<?php

if (!empty($_POST)) {

	require("config.inc.php");
	
	$tablename=$_POST['tablename'];
	 
	//HL: query that we are going to execute
	//:user here is the variable that we are going to fill in the value in the next line
	$query = "SELECT * FROM $tablename";
	 
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
	 
	// HL: retrieve the data using fetchall() and populate it into $rows
	$rows = $stmt->fetchAll();
	 
	 
	if ($rows) {
	    $response["success"] = 1;
	    $response["message"] = "Post Available!";
	    $response["posts"]   = array();
	     
	    foreach ($rows as $row) {
			
	        $post = array();
			$post["day"]    = $row["day"]; 
	        $post["iscomplete"] = $row["iscomplete"];
	        $post["message"]    = $row["message"];
			
	        array_push($response["posts"], $post);
	    }
	     
	    // echoing JSON response
	    echo json_encode($response);
	     
	     
	} else {
	    $response["success"] = 0;
	    $response["message"] = "No Post Available!";
	    die(json_encode($response));
	}
	 
} else {
?>
    <h1>Retrieve Dates</h1>
    <form action="fillprofile.php" method="post">
		TableName:<br />
        <input type="text" name="username" value="" />
        <br /><br />
        <input type="submit" value="Add" />
	 </form>
<?php
	}
?>
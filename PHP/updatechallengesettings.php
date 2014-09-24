<?php
	 
	require("config.inc.php");
	 
	if (!empty($_POST)) {
	    if (empty($_POST['name']) || empty($_POST['description'])|| empty($_POST['category'])
		||empty($_POST['start_date']) || empty($_POST['end_date'])|| empty($_POST['days_of_week'])) {
	         
	        $response["success"] = 0;
	        $response["message"] = "Please Fill in all the fields";

	        die(json_encode($response));
	    }
		
		
	/*
		INSERT CHALLENGE INTO CHALLENGES_MAIN DATABASE 
	*/
		
		//Retrieve variables from android
		
	$id=$_POST['challenge_id'];
	
	$description=$_POST['description'];
	$category=$_POST['category'];
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$name=$_POST['name'];
	$days_of_week=$_POST['days_of_week'];

	$query = "UPDATE challenges_main 
				SET name='$name', description='$description', category='$category', 
						start_date='$start_date', end_date='$end_date', 
						days_of_week='$days_of_week'
				WHERE id='$id'";
		
	//time to run our query, and create the user
	try {
		$stmt   = $db->prepare($query);
		$stmt->execute();
	}
	catch (PDOException $ex) {
		$response["success"] = 0;
		$response["message"] = "Unable to add challenge into database";
		die(json_encode($response));
	}
		
	/*
		 
		FIND ID OF THE NEW CHALLENGE
		 
	*/

	$response["success"] = 1;
	$response["message"] = "Challenge Successfully Added!";
	echo json_encode($response);
	     
	} else {
?>
    <h1>Update Challenge</h1>
    <form action="updatechallengesettings.php" method="post">
	Name:<br />
        	<input type="text" name="name" value="" />
        <br /><br />
        
        Description:<br />
        	<input type="text" name="description" value="" />
        <br /><br />
        
	Category<br />
        	<input type="text" name="category" value="" />
        <br /><br />
        
	Start Date:<br />
        	<input type="text" name="start_date" value="" />
        <br /><br />
        
	End Date:<br />
        	<input type="text" name="end_date" value="" />
        <br /><br />
        
	User 1:<br />
        	<input type="text" name="user_1" value="" />
        <br /><br />
        
        User 2:<br />
        	<input type="text" name="user_2" value="" />
        <br /><br />
        
	Image ID:<br />
        	<input type="text" name="image_id" value="" />
        <br /><br />
	
        <input type="submit" value="Add" />
	 </form>
<?php
	}
?>



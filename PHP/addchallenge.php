<?php
	 
	require("config.inc.php");
	 
	if (!empty($_POST)) {
	    if (empty($_POST['name']) || empty($_POST['description'])|| empty($_POST['category'])
		||empty($_POST['start_date']) || empty($_POST['end_date'])||empty($_POST['user_1'])||empty($_POST['user_2'])) {
	         
	        $response["success"] = 0;
	        $response["message"] = "Please Fill in all the fields";

	        die(json_encode($response));
	    }
	     
		 /*
				CHECK IF CHALLENGE IS ALREADY IN DATABASE
		 */
		 
		 
		 //checks if entered challenge is already in database
	    $query	= " SELECT 1 FROM challenges_main WHERE name = :name";
	    $query_params = array(
	        ':name' => $_POST['name']
	    );
	     
	    try {
	        $stmt   = $db->prepare($query);
	        $result = $stmt->execute($query_params);
	    }
	    catch (PDOException $ex) {
	        $response["success"] = 0;
	        $response["message"] = "Database Error1. Please Try Again!";
	        die(json_encode($response));
	    }
	     
		//retrieves the row of data from query
	    $row = $stmt->fetch();
	    if ($row) {
	        $response["success"] = 0;
	        $response["message"] = "Error: this challenge is already there";
	        die(json_encode($response));
	    }
		
		
		/*
		 
				INSERT CHALLENGE INTO CHALLENGES_MAIN DATABASE
		 
		*/
		
		//Retrieve variables from android
		
		$name=$_POST['name'];
		$description=$_POST['description'];
		$category=$_POST['category'];
		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		$user_1= $_POST['user_1'];
		$user_2= $_POST['user_2'];
		$image_id = $_POST['image_id'];
		$days_of_week=$_POST['days_of_week'];

		$query = "INSERT INTO challenges_main ( name, description, category, start_date, end_date, user_1, user_2, image_id, days_of_week) 
				VALUES ( '$name', '$description', '$category', '$start_date', '$end_date', '$user_1', '$user_2','$image_id','$days_of_week')";
			
		//time to run our query, and create the user
		try {
			$stmt   = $db->prepare($query);
			$stmt->execute();
			//$result = $stmt->execute($query_params);
		}
		catch (PDOException $ex) {
			$response["success"] = 0;
			$response["message"] = "Unable to add challenge into database";
			die(json_encode($response));
		}
			
		$response["success"] = 1;
		$response["message"] = "Challenge Successfully Added!";
		echo json_encode($response);
		
		//LEGACY CODE
		
		/*
		$start = strtotime($start_date);
		$end = strtotime($end_date);
		$datediff = floor(($end - $start)/(60*60*24));
		//echo floor($datediff/(60*60*24));
		$date=$start_date;
		
		for ($x=0; $x<=$datediff; $x++) {
			
			$query = "INSERT INTO updates (challenge_id, user_id, date, message) 
				VALUES ( '$challenge_id','$user_1','$date', ''), ('$challenge_id','$user_2','$date', '')";
				
			try {
				$stmt   = $db->prepare($query);
				$stmt->execute();
				
			}
			catch (PDOException $ex) {
				$response["success"] = 0;
				$response["message"] = "Database Error2. Please Try Again!";
				die(json_encode($response));
			}
			$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
			
		} 
		*/
	     
	} else {
?>
    <h1>Add Challenge</h1>
    <form action="addchallenge.php" method="post">
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



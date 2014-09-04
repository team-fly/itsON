<?php
	 
	require("config.inc.php");
	 
	if (!empty($_POST)) {
	    if (empty($_POST['username']) || empty($_POST['password'])|| empty($_POST['name'])||empty($_POST['email'])) {
	         
	        $response["success"] = 0;
	        $response["message"] = "Please Fill in all the fields";

	        die(json_encode($response));
	    }
		
		//////////////////////////////////////////////////////////////////////
	   
	   //////////		Checks if User is already in Database 	//////////////
	   
	   ///////////////////////////////////////////////////////////////////////
		 
	    $query        = " SELECT 1 FROM users WHERE username = :user";
	    $query_params = array(
	        ':user' => $_POST['username']
	    );
	     

	    try {
	        $stmt   = $db->prepare($query);
	        $result = $stmt->execute($query_params);
	    }
	    catch (PDOException $ex) {

	        $response["success"] = 0;
	        $response["message"] = "Error Accessing Database";
	        die(json_encode($response));
	    }
	     
	    $row = $stmt->fetch();
	    if ($row) {
	        $response["success"] = 0;
	        $response["message"] = "Username is already in use";
	        die(json_encode($response));
	    }
		
		
		///////////////////////////////////////////////////////////////
	   
	   //////////		Insert User into Users Database	//////////////
	   
	   ///////////////////////////////////////////////////////////////
	     
	    $query = "INSERT INTO users ( username, password, fullname, emailaddr ) VALUES ( :user, :pass, :name, :email ) ";
	     
	    //Again, we need to update our tokens with the actual data:
	    $query_params = array(
	        ':user' => $_POST['username'],
	        ':pass' => $_POST['password'],
			':name' => $_POST['name'],
	        ':email' => $_POST['email']
	    );
	     
	    //time to run our query, and create the user
	    try {
	        $stmt   = $db->prepare($query);
	        $result = $stmt->execute($query_params);
	    }
	    catch (PDOException $ex) {
	        $response["success"] = 0;
	        $response["message"] = "Error Accessing Database";
	        die(json_encode($response));
	    }
	   
	   ///////////////////////////////////////////////////////////////
	   
	   //////////		Retrieve Newly Added User ID 	//////////////
	   
	   ///////////////////////////////////////////////////////////////
		
		$query	= "SELECT MAX(user_id) AS 'user_id' FROM users";
	     
	    try {
	        $stmt   = $db->prepare($query);
	        $stmt->execute();
	    }
	    catch (PDOException $ex) {
	        $response["success"] = 0;
	        $response["message"] = "Error Adding User to Database";
	        die(json_encode($response));
	    }

	    $row = $stmt->fetch();
		$user_id=$row["user_id"];
		
		$dpAddress='http://team-fly.com/teamflyc_webserver/display_picture/'.$user_id.'.jpg';
		$coverAddress='http://team-fly.com/teamflyc_webserver/cover_picture/'.$user_id.'.jpg';
		
		///////////////////////////////////////////////////////////////
	   
	   //////////			Update DP Address 			//////////////
	   
	   ///////////////////////////////////////////////////////////////
		
		$query	= "UPDATE users 
					SET dp_url='$dpAddress',
						cover_url='$coverAddress'
					WHERE user_id='$user_id'";
	     
	    try {
	        $stmt   = $db->prepare($query);
	        $stmt->execute();
	    }
	    catch (PDOException $ex) {
	        $response["success"] = 0;
	        $response["message"] = "Error Adding User Database";
	        die(json_encode($response));
	    }
		
		
		///////////////////////////////////////////////////////////////
	   
	   //////////		Sets up Friends Entry for User 	//////////////
	   
	   ///////////////////////////////////////////////////////////////
		
		/*
		$query="INSERT INTO friends
				(friend_one,friend_two,status)
				VALUES
				('$user_id','$user_id','2')";
		
		try {
	        $stmt   = $db->prepare($query);
	        $stmt->execute();
	    }
	    catch (PDOException $ex) {
	        $response["success"] = 0;
	        $response["message"] = "Error Setting up Friends Database";
	        die(json_encode($response));
	    }
		*/
		
		$response["user_id"]= $user_id;
		$response["success"] = 1;
	    $response["message"] = "User Successfully Added!";
		
	    echo json_encode($response);
	     
	} else {
?>
    <h1>Register</h1>
    <form action="register.php" method="post">
		Username:<br />
        <input type="text" name="username" value="" />
        <br /><br />
        Password:<br />
        <input type="password" name="password" value="" />
        <br /><br />
		Name:<br />
        <input type="text" name="name" value="" />
        <br /><br />
		Email:<br />
        <input type="text" name="email" value="" />
        <br /><br />
        <input type="submit" value="Register New User" />
	 </form>
<?php
	}
?>
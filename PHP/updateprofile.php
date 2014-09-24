<?php
	 
	require("config.inc.php");
	 
	if (!empty($_POST)) {
	
		$user_id=$_POST['user_id'];
		$username=$_POST['username'];
	
		//////////////////////////////////////////////////////////////////////
	   
	   //////////		Checks if User is already in Database 	//////////////
	   
	   ///////////////////////////////////////////////////////////////////////
		 
	    $query = " SELECT 1 FROM users 
					WHERE username = '$username'
					AND user_id !='$user_id'";
	     

	    try {
	        $stmt   = $db->prepare($query);
	        $stmt->execute();
	    }
	    catch (PDOException $ex) {

	        $response["success"] = 0;
	        $response["message"] = "Error Accessing Database1";
	        die(json_encode($response));
	    }
	     
	    $row = $stmt->fetch();
	    if ($row) {
	        $response["success"] = 0;
	        $response["message"] = "Username is already in use";
	        die(json_encode($response));
	    }
		
		
		///////////////////////////////////////////////////////////////
	   
	   //////////		Update User into Users Database	//////////////
	   
	   ///////////////////////////////////////////////////////////////

		$query="UPDATE users 
				SET username=:user, password=:pass, 
					fullname=:name, emailaddr=:email 
				WHERE user_id='$user_id'";
	     
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
	        $response["message"] = "Error Accessing Database2";
	        die(json_encode($response));
	    }
		
		$response["success"] = 1;
	    $response["message"] = "User Profile Updated!";
		
	    echo json_encode($response);
	     
	} else {
?>
    <h1>Update Profile</h1>
    <form action="updateprofile.php" method="post">
		Id:<br />
        <input type="text" name="user_id" value="" />
        <br /><br />
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
        <input type="submit" value="Update User" />
	 </form>
<?php
	}
?>
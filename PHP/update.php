<?php
	 
require("config.inc.php");

if (!empty($_POST)) {
	
	$user_id=$_POST['user_id'];
	$challenge_id= $_POST['challenge_id'];
	$update_message=$_POST['update_message'];
	$date=date('Y-m-d'); 
	
	
	//////////////////////////////////////////////////////////////////////
	   
	//////////		SET CHALLENGE TO COMPLETE TO TODAY	 	//////////////
	  
	///////////////////////////////////////////////////////////////////////
    
	$query = "UPDATE updates
			SET iscomplete='1', message='$update_message'
			WHERE date='$date'
			AND user_id='$user_id'
			AND challenge_id='$challenge_id'" ;
     
	try {
	    $stmt   = $db->prepare($query);
	    $stmt->execute(); 
		//die(json_encode($response));
	}
	catch (PDOException $ex) {
	    $response["success"] = 0;
	    $response["message"] = "Unable to update table";
	     die(json_encode($response)); 
	}
	$response["success"] = 1;
	$response["message"] = "Update successful!";
	echo json_encode($response);
	
} else {
//HL: why is this else placed here? does it mean the script is done?
?>
        <h1>Update</h1>
        <form action="update.php" method="post">
        User ID<br />
            <input type="text" name="user_id" value=""/>
        <br /><br />
        Challenge ID<br />
            <input type="text" name="challenge_id" value=""/>
        <br /><br />
        <input type="submit" value="Add" />
        </form>
    <?php
} 
?> 
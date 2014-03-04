<?php
	session_start();
	ini_set('display_errors', 'On');
	include 'password.php';

	// open the database
	$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "dorweilj-db", $myPWD, "dorweilj-db");
	if($mysqli->connect_errno){
        echo "Failed to connect to mysql: (" . $mysqli->errno . ")" . $mysqli->connect_error;
    } 

    $user = NULL;
    $type = NULL;
    $pass = NULL;
    $email = NULL;
    $location = NULL;
    $track1 = NULL; $track2 = NULL; $track3 = NULL; $track4 = NULL; $track5 = NULL;  $track6 = NULL; $track7 = NULL;
    $track8 = NULL; $track9 = NULL; $track10 = NULL; $genre = NULL;

	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$type = $_POST['type'];

		// check user password
		if($type == 1){
    	$user = $_POST['userName']; 
    	$pass = $_POST['Pass'];
    	}

    	if($type == 2){
    	$user = $_POST['userName']; 
    	$pass = $_POST['Pass'];
    	$email = $_POST['Email'];
    	$location = $_POST['Zip'];
    	$genre = $_POST['Genre'];
    	}

    	if($type == 5){
    		//check if there is a session
    		//get out if no session
    		if(!isset($_SESSION['username'])){
    			echo 0;
    			exit();
    		}
    		$user = $_SESSION["username"];
    	}

    	if($type == 6){
    		session_destroy();
    		exit();
    	}
	}
		// variables to hold database values
	$out_id = NULL;
	$out_pass = NULL;
	$passCheck = NULL;
	$out_email = NULL;
  
	// check to see what type of request we got.
	// options are 1: verify password, 2: add user to DB, 3: push playlist to DB, 4: get playlist from DB

	 // add user to database then send their data back to get player started.
	if($type == 2){

		if (!($stmt = $mysqli->prepare("SELECT user, email FROM soundDB WHERE user='$user' LIMIT 1"))) {
	    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_result($out_user, $out_email)) {
	    	echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}

		$stmt->fetch();

		if($out_user){
			echo "error 1";
			exit();
		}

		$stmt->close();
		// check for existing emails
		if (!($stmt = $mysqli->prepare("SELECT email FROM soundDB WHERE email='$email' LIMIT 1"))) {
	    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_result($out_email)) {
	    	echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}

		$stmt->fetch();

		if($out_email){
			echo "error 2";
			exit();
		}

		


	if (!($mysqli->query("INSERT INTO soundDB(user,Pass,email,location,genre) VALUES ('$user','$pass','$email','$location','$genre')"))) {
	    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		echo json_encode(array( 'user' => $user, 'genre' => $genre));
		$mysqli->close();
		$_SESSION["username"] = $user;
		exit();
	}

	// push the playlist stack back to the database.
	if($type == 3){ 
		if (!($mysqli->query("INSERT INTO soundDB(user,Pass,email,location,genre) VALUES ('$user','$pass','$email','$location','$genre')"))) {
	    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		$mysqli->close();
		exit();
	}


	// user needs to be validates or is logged in. 
	if($type == 1){
	    //Get data to validate password
		if (!($stmt = $mysqli->prepare("SELECT user, Pass FROM soundDB WHERE user='$user'"))) {
	    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_result($out_user, $out_pass)) {
	    	echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if($stmt->fetch()) 
			if(strcasecmp((string)$out_pass, (string)$pass) == 0){
				$passCheck = 1;
			}
			else
				echo "error";
		/* close statement */
	    $stmt->close();
	}

	    // we have a good password or user is logged in already
	if($passCheck == 1 || $type == 5){
		$stmt = $mysqli->prepare("SELECT user, email, location, genre, track1 FROM soundDB WHERE user='$user' LIMIT 1");
		$stmt->execute();
		$stmt->bind_result($out_user, $email, $location, $genre, $track1);
		if($stmt->fetch())
			echo json_encode(array( 'user' => $out_user, 'email' => $email, 
				'location' => $location, 'genre' => $genre, 'track1' => $track1));
		$_SESSION["username"] = $out_user;
	
		 /* close statement */
	    $stmt->close();

		/* close connection */
		$mysqli->close();

		exit();
	}

		/* close connection */
		$mysqli->close();
?>
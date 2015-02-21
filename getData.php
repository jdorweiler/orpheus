<?php
	session_start();
	ini_set('display_errors', 'On');
	include 'password.php';

	// open the database
	$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "dorweilj-db", $myPWD, "dorweilj-db");
	if($mysqli->connect_errno){
        echo "Failed to connect to mysql: (" . $mysqli->errno . ")" . $mysqli->connect_error;
    } 

    $id = NULL;
    $user = NULL;
    $type = NULL;
    $pass = NULL;
    $email = NULL;
    $location = NULL;
    $playlist = NULL;

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

    	if($type == 3){
    		$playlist = $_POST['playlist'];
    	}

    	if($type == 5){
    		//check if there is a session
    		//get out if no session
    		if(!isset($_SESSION['username'])){
    			echo 0;
    			exit();
    		}
    		$id = $_SESSION["username"];
    	}

    	if($type == 6){
    		session_destroy();
    		exit();
    	}

    	if($type == 7){
	    	$email = $_POST['Email'];
	    	$location = $_POST['Zip'];
	    	$genre = $_POST['Genre'];
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

		if (!($stmt = $mysqli->prepare("SELECT name, email FROM users WHERE name='$user' LIMIT 1"))) {
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
		if (!($stmt = $mysqli->prepare("SELECT email FROM users WHERE email='$email' LIMIT 1"))) {
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

        // insert a new user in to the users table
        // this happens when a new user signs up
		if (!($mysqli->query("INSERT INTO users(name,pass,email,location,genre) VALUES ('$user','$pass','$email','$location','$genre')"))) {
		    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

        // for frontend logging
		echo json_encode(array( 'user' => $user, 'email' => $email, 'location' => $location, 'genre' => $genre));

        // get the id for the new user 
        if (!($mysqli->query("SELECT id from users where name=$user"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

        // bind the id and return it as a session username
        if (!($stmt->bind_result($id))){
            echo "Binding user id failedi (" . $stmt->errno . ") " . $stmt->error;
        }
		$mysqli->close();
		$_SESSION["username"] = $id;
		exit();
	}

    // push updated playlist to user_playlist
	if($type == 3){
        	$id = $_SESSION["username"];
        	echo "updating playlist for user: $id";

        	// play list is a json string of songs containing the users current playlist 
       	 	// update the user_playlist db with the new playlist
		    echo "playlist: $playlist";      
        	// deserialize json
        	$decoded_json = json_decode($playlist, true);

		foreach($decoded_json as $song){

            // update the song table with any new songs
			if (!($mysqli->query("INSERT INTO songs values('', $song[title], $song[track])"))){
	    			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }
            $song_id = NULL;
            // get the song ID
            $stmt = $mysqli->prepare("Select song_id from songs where url=$song->title and name=$song->track");
            $stmt->execute();
            $stmt->bind_result($song_id);

			if (!($mysqli->query("UPDATE userPlaylist SET song_id=$song_ide, user_id=$id"))){
	    			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }
            exit();
		}

	}


	// user needs to be validates or is logged in. 
	if($type == 1){
	    //Get data to validate password
		if (!($stmt = $mysqli->prepare("SELECT id, name, pass FROM users WHERE name='$user'"))) {
	    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_result($id, $out_user, $out_pass)) {
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
		$stmt = $mysqli->prepare("SELECT id, name, email, location, genre FROM users WHERE id='$id' LIMIT 1");
		$stmt->execute();
		$stmt->bind_result($id, $out_user, $email, $location, $genre);
		if($stmt->fetch())
			echo json_encode(array( 'user' => $out_user, 'email' => $email, 
				'location' => $location, 'genre' => $genre, 'playlist' => $playlist));
        
        // send the username back to frontend
        $_SESSION["username"] = $id;
	
		 /* close statement */
	    $stmt->close();

		/* close connection */
		$mysqli->close();

		exit();
	}

	if($type == 7){
		$id = $_SESSION["username"];
		if (!($mysqli->query("UPDATE users SET email='$email',genre='$genre',location='$location' WHERE id='$id'"))){
		    	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			exit();
	}


	/* close connection */
	$mysqli->close();
?>

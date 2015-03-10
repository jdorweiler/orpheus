<?php
	session_start();
	ini_set('display_errors', 'On');
	include 'password.php';

	// open the database
	$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "dorweilj-db", $myPWD, "dorweilj-db");
	if($mysqli->connect_errno){
        echo "Failed to connect to mysql: (" . $mysqli->errno . ")" . $mysqli->connect_error;
    } 

	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$type = $_POST['type'];
        $user = $_SESSION["username"];
        $to_subscribe = $_POST['toSub'];

        ///  get the playlists that the user is subscribed to
        $playlists = NULL;
		if (!($stmt = $mysqli->prepare("SELECT subscribed_id FROM subedPlaylist WHERE subed_user_id='$user'"))) {
	        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_result($playlists)) {
	    	echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
        $stmt->fetch();
        $stmt->close();
        
        // subscribe the user to a new playlist
        if( strcmp($type, "subscribe") == 0){

            $subscribe_to_id = NULL;

            // get the user's id (the one to subscribe to)
            if (!($stmt = $mysqli->prepare("select id from users where name='$to_subscribe'"))) {
                echo "gettting user id failed";
            }
            $stmt->execute();
            $stmt->bind_result($subscribe_to_id);
            $stmt->fetch();
            $stmt->close();

            echo "User: $user";
            echo "subscribe to: $subscribe_to_ia";

            if(!$subscribe_to_id){
                $mysqli->close();
                exit();
            }

            $exists = NULL;

		    if (!($stmt = $mysqli->prepare("select subscribed_id from subedPlaylist where subscribed_id=$user' and subed_user_id='$subscribe_to_id'"))) {
	    	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		    }
		    if (!$stmt->execute()) {
	    	    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		    }
            $stmt->bind_result($subscribe_to_id);
		    $stmt->fetch();
            $mysqli->close();

            if($exists){
                $mysqli->close();
                exit();
            }
        
            // add the new subscription
		    if (!($stmt = $mysqli->prepare("Insert into subedPlaylist values('$user', '$subscribe_to_id')"))) {
	    	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		    }
		    if (!$stmt->execute()) {
	    	    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		    }
		    $stmt->fetch();
            $mysqli->close();
            exit();
        
        } 
    }

?>

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
        $user = $_POST["user"];
        $to_subscribe = $_POST['toSub'];

        ///  get the playlists that the user is subscribed to
        $playlists = NULL;
		if (!($stmt = $mysqli->prepare("SELECT playlist_id FROM playlist_subs WHERE user_id='$user'"))) {
	        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->execute()) {
	    	echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_result($playlists)) {
	    	echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$stmt->fetch();
        $mysqli->close();
        
        // subscribe the user to a new playlist
        if($type eq "subscribe"){
            // loop through the playlists and bail if we see
            // the same subscription
            foreach($playlists as $playlist){
                if($playlists eq $user){
                    exit();
                }
            }

            // add the new subscription
		    if (!($stmt = $mysqli->prepare("Insert into playlist_subs playlist_id values('$to_subscribe', '$user')"))) {
	    	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		    }
		    if (!$stmt->execute()) {
	    	    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
		    }
		    if (!$stmt->bind_result($playlists)) {
	    	    echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		    }
		    $stmt->fetch();
            $mysqli->close();
                
            
                exit();
        
        } 
        else {
            // just getting the playlists, nothing to add
            exit();
        }

?>

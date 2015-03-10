<?php
	session_start();
	ini_set('display_errors', 'On');
	include 'password.php';

    // return the playlist for a user
    function get_user_playlists( $id ) {

        $rows = array();

        if (!($stmt = mysqli_query($mysqli,"SELECT S.name, S.url from userPlaylist PL inner join songs S on S.id = PL.song_id where PL.user_id='$id'"))) {
            echo "gettting user playlist failed";
        }

        while($r = mysqli_fetch_asssoc($stmt)){
            $rows[] = $r;
        }
        echo $playlist = json_encode(array('playlist' => $playlist));
    }


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
        $mysqli->close();
        
        // subscribe the user to a new playlist
        if( strcmp($type, "subscribe") == 0){
            echo "got subscribe";
            echo $to_subscribe;

            $subscribe_to_id = NULL;

            // get the user's id (the one to subscribe to)

            if (!($stmt = $mysqli->prepare("select id from users where name='$to_subscribe'"))) {
                echo "gettting user id failed";
            }
            $stmt->execute();
            $stmt->bind_result($subscribe_to_id);
            $stmt->fetch();
            $stmt->close();

            // loop through the playlists and bail if we see
            // the same subscription
            foreach($playlists as $playlist){
                if( strcmp($playlists, $subscribed_to_user) == 0){
                    $mysqli->close();
                    exit();
                }
            }

            // add the new subscription
		    if (!($stmt = $mysqli->prepare("Insert into subedPlaylist values('$user', '$to_subscribe')"))) {
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
            get_user_playlists($to_subscribe);
            exit();
        }
    }


?>

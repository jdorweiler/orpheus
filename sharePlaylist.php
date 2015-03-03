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
            get_user_playlist($to_subscribe);
            exit();
        }
    }

    // return the playlist for a user
    function get_user_playlists( $id ) {

        $rows = array();

        if (!($stmt = mysqli_query($mysqli,"SELECT S.name, S.url from userPlaylist PL inner join songs S on S.id = PL.song_id where PL.user_id='$id'"))) {
            echo "gettting user playlist failed"
        }

        while($r = mysqli_fetch_asssoc($stmt){
            $rows[] = $r;
        }
        echo $playlist = json_encode(array( 'user' => $out_user, 'email' => $email, 'location' => $location, 'genre' => $genre, 'playlist' => $playlist));
    }



?>

<?php
// get a list of all user's playlists.  This loads in the playlist modal so a
// user can pick a new playlist to subscribe to

	session_start();
	ini_set('display_errors', 'On');
	include 'password.php';

	// open the database
	$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "dorweilj-db", $myPWD, "dorweilj-db");
	if($mysqli->connect_errno){
        echo "Failed to connect to mysql: (" . $mysqli->errno . ")" . $mysqli->connect_error;
    } 

	if($_SERVER['REQUEST_METHOD'] == "POST"){
        $id = $_SESSION["username"];

        ///  get the playlists that the user is subscribed to
        $playlists = NULL;
        $users = array();

        // get 5 or so users playlists to show on the frontend
        if (!($stmt = mysqli_query($mysqli,"SELECT id from users where id!='$id' LIMIT 5"))) {
            echo "problem getting users";
        }

        while($r = mysqli_fetch_assoc($stmt)){
            $users[] = $r;
        }

        $stmt->close();

        foreach($users as $user){
            $playlist = NULL;

            if (!($stmt = mysqli_query($mysqli,
                "SELECT S.name, S.url, U.name from userPlaylist PL 
                    inner join songs S on S.id = PL.song_id 
                    inner join users U on U.id=PL.user_id where PL.user_id='$user[id]'"))) 
            {
                echo "problem getting playlist for user";
            }

            while($r = mysqli_fetch_assoc($stmt)){
                $playlist[] = $r;
                $username = $r['name'];
            }

            $playlists[$username] = $playlist;

            $stmt->close();
        }

        echo json_encode(array('user_playlists' => $playlists));

        $mysqli->close();
        exit();


    }

?>

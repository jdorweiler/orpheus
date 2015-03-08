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
        $playlists = array();
        $users = array();

        // get 5 or so users playlists to show on the frontend
        if (!($stmt = mysqli_query($mysqli,"SELECT id, name from users where id!='$id' LIMIT 5"))) {
            echo "problem getting users";
        }

        while($r = mysqli_fetch_assoc($stmt)){
            array_push($users, $r['name']);
        }

        $stmt->close();

        echo json_encode(array('users' => $users));

        $mysqli->close();
        exit();


    }

?>

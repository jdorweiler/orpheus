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
        $id = $_SESSION["username"];
        $users = NULL;
        
        echo "getting users playlist subscriptions";
        $subscriptions = array();
        $rows = array();
        
        if (!($stmt = mysqli_query($mysqli,"select U.name from users U inner join
            (SELECT sP.subscribed_id from subedPlaylist where subed_user_id='$id') as T1 
            U.id = T1.subscribed_id"))) {
          echo "gettting user subscriptions failed";
        }
        
        while($r = mysqli_fetch_array($stmt)){
            $rows[] = $r;
        }

        echo $users = json_encode(array('subscriptions' => $rows));
    }

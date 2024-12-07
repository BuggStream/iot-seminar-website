<?php
	session_start();
	/* Lets check if it can log in */

	/* Section Reserved for Admin Sys*/

	if(isset($_SESSION['Username']) && $_SESSION['Username']!=null && isset($_SESSION['accountperm']) && $_SESSION['accountperm']>0){
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="devicelist"){

            $dbUser="root";
            $dbPsw="ZTjbHmXX6Rodg98ou&fuo3^!";
            $dbName="my_wherethefuckismydevice";
            $dbAddress="localhost";
			// Create connection
			$conn = new mysqli($dbAddress, $dbUser, $dbPsw, $dbName);

			// Check connection
			if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
			}

			// prepare and bind
			$stmt = $conn->prepare("SELECT * FROM devices");
			//$stmt->bind_param("i", $perm);

			// set parameters and execute

			$stmt->execute();
			$result= $stmt->get_result();
		
			$deviceIdList = array();
			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {
					array_push($deviceIdList, $row['deviceid']);
				}
				
			}
			
			echo "{\"session\":1, \"status\":\"ok\", \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm'].", \"Data\": ".json_encode($deviceIdList)."}";
			return;
			
			
			
			
			
		}
		
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="users"){

            $dbUser="root";
            $dbPsw="ZTjbHmXX6Rodg98ou&fuo3^!";
            $dbName="my_wherethefuckismydevice";
            $dbAddress="localhost";
			// Create connection
			$conn = new mysqli($dbAddress, $dbUser, $dbPsw, $dbName);

			// Check connection
			if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
			}

			// prepare and bind
			$stmt = $conn->prepare("SELECT * FROM users WHERE accountperm<? ");
			
			if($_SESSION['accountperm']==2) $perm = 3;
			else $perm = $_SESSION['accountperm'];
			$stmt->bind_param("i", $perm);

			// set parameters and execute

			$stmt->execute();
			$result= $stmt->get_result();
		
			
			if ($result->num_rows > 0) {
			  // output data of each row
				$usersAndDevs = array();
				
				while($row = $result->fetch_assoc()) {
					$usersAndDevs[$row["username"]]=json_decode($row['devices'], true)["devices"];

				}
			}
			
			echo "{\"session\":1, \"status\":\"ok\", \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm'].", \"Data\": ".json_encode($usersAndDevs)."}";
			return;
			
			
			
			
			
		}
		
		
		/* Update Permission on Devices*/
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="changedevices" && isset($_REQUEST['user']) && isset($_REQUEST['devices'])   ){
			
			/* Check if you can do it first*/
            $dbUser="root";
            $dbPsw="ZTjbHmXX6Rodg98ou&fuo3^!";
            $dbName="my_wherethefuckismydevice";
            $dbAddress="localhost";
			// Create connection
			$conn = new mysqli($dbAddress, $dbUser, $dbPsw, $dbName);

			// Check connection
			if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
			}

			// prepare and bind
			$stmt = $conn->prepare("SELECT * FROM users WHERE username=? ");
			$stmt->bind_param("s", $_REQUEST['user']);

			// set parameters and execute

			$stmt->execute();
			$result= $stmt->get_result();
			
			
			if ($result->num_rows == 1) {
			  /* Check Permission Rank*/
				
				$row = $result->fetch_assoc(); 
				
				if($row['accountperm']<$_SESSION['accountperm'] ||  $_SESSION['accountperm'] == 2){
					/* Proceed */
					// prepare and bind
					$newDeviceList = array();
					$newDeviceList['devices'] = json_decode($_REQUEST['devices'], true);
					
					$stmt = $conn->prepare("UPDATE users SET devices=? WHERE username=?");
					$stmt->bind_param("ss",json_encode($newDeviceList),$_REQUEST['user']);

					// set parameters and execute

					$stmt->execute();
					$result= $stmt->get_result();
			
			
					
					
				}else{
					echo "{\"session\":1, \"status\":\"notAllowed\", \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm']."}";
					return;
			
				}

				
			}
			echo "{\"session\":1, \"status\":\"ok\", \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm']."}";
			return;
		
		
		
		}
		
		
		
		
		
		
	/* Anybody Functions */	
	}

	if(isset($_SESSION['Username']) && $_SESSION['Username']!=null){
		if(isset($_POST['mode']) && $_POST['mode']=="changepsw" && isset($_POST['pswold']) && isset($_POST['pswnew'])   ){

			$done = "{\"session\":1, \"status\":\"error\", \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm']."}";

            $dbUser="root";
            $dbPsw="ZTjbHmXX6Rodg98ou&fuo3^!";
            $dbName="my_wherethefuckismydevice";
            $dbAddress="localhost";

			$conn = new mysqli($dbAddress, $dbUser, $dbPsw, $dbName);


			if ($conn->connect_error) {
			  die("Connection failed: " . $conn->connect_error);
			}

			// prepare and bind
			$stmt = $conn->prepare("SELECT * FROM users WHERE username=? ");
			
			$stmt->bind_param("s", $_SESSION['Username']);

			// set parameters and execute

			$stmt->execute();
			$result= $stmt->get_result();
		
			
			if ($result->num_rows == 1) {
			  // output data of each row
				
				$row = $result->fetch_assoc(); 
				
				/* Password's OK*/
				if(password_verify($_POST['pswold'],$row["pass"])){

					$newPass=password_hash($_POST['pswnew'], PASSWORD_BCRYPT);

					$conn = new mysqli($dbAddress, $dbUser, $dbPsw, $dbName);

					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					}

					$stmt = $conn->prepare("UPDATE users SET pass=? WHERE username=? ");
					$stmt->bind_param("ss",$newPass, $_SESSION['Username']);

					$stmt->execute();
					$result= $stmt->get_result();

					$done = "{\"session\":1, \"status\":\"ok\", \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm']."}";
				}


			}
			
			echo $done;
			return;
		}
	
	}else{
		echo "{\"session\":0}";
		//header('Location: index.html?error=nologin');
	}

?>
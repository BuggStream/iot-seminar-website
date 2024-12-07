<?php
	session_set_cookie_params(86400); 
	session_start();
	//var_dump($_SESSION);
	if(isset($_SESSION['Username']) && $_SESSION['Username']!=null){
		echo "{\"session\":1, \"preset\":1, \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm']."}";
	}
  elseif(!isset($_POST['Username']) ||! isset($_POST['Password'])) {
	echo "{\"session\": 0}";
	//header('Location: index.html?error=nologin');
  }
  else{
	

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
	$stmt = $conn->prepare("SELECT * FROM users WHERE BINARY username=? ");
	$stmt->bind_param("s", $_POST['Username']);

	// set parameters and execute
	
	$stmt->execute();
	$result= $stmt->get_result();
	 
	  
	//$hashed=md5($_POST['Password']);
	//echo $hashed;
	$row =1;
	$gotpsw =0;
	  //$result = $conn->query($stmt);
	if ($result->num_rows > 0) {
	  // output data of each row
	  while($row = $result->fetch_assoc()) {
		if(password_verify($_POST['Password'], $row['pass'])){
			$_SESSION['Username']=$_POST['Username'];
			$_SESSION['devices']=json_decode($row['devices'], true)["devices"];
			$_SESSION['timezone']=0;
			$_SESSION['accountperm']=$row['accountperm'];
			//print_r(json_decode($row['devices'], false));
			echo "{\"session\": 1, \"Username\":\"".$_SESSION['Username']."\", \"Perm\": ".$_SESSION['accountperm']."}";
			//header('Location: track.php');
		}else{
			echo "{\"session\": 0}";
			//header('Location: index.html?error=badlogin');
		}
		
	  }
	} else {
		echo "{\"session\":0}";
	  	//header('Location: index.html?error=badlogin');
	}
	$stmt->close();

  } 

?>
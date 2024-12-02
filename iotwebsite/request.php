<?php
//ANY USEFUL? CLOUDFLARE MIGHT SPOOF!
	session_set_cookie_params(86400); 
	session_start();
	
	//var_dump($_SESSION);

	/* Checks if i'm entitled to track this device*/
	if(isset($_POST['device']) &&  in_array($_POST['device'],$_SESSION['devices'])){
		
	}elseif(!isset($_POST['device']) && isset($_SESSION['devices'])){
		echo "{\"status\": \"ok\", \"available\": ".json_encode($_SESSION['devices'])."}";
		return;
	}elseif(!isset($_SESSION['devices'])){
		echo "{\"status\": \"notAllowed\"}";
		return;
	}
	else{
		/* Return devices list*/
		//http_response_code(403);
		echo "{\"status\": \"notAllowed\", \"deviceid\": ".$_POST['device'].", \"available\": ".json_encode($_SESSION['devices'])."}";
		return;
	}
	

	$dbUser="wherethefuckismydevice";
	$dbPsw="8@cEn4dn7yzxrEbQ3";
	$dbName="my_wherethefuckismydevice";
	$dbAddress="localhost";
	// Create connection
	$conn = new mysqli($dbAddress, $dbUser, $dbPsw, $dbName);

	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}

	// prepare and bind
	$stmt = $conn->prepare("SELECT * FROM devices WHERE deviceid=? ");
	$deviceid= $_POST['device'];
	$stmt->bind_param("i", $deviceid);

	// set parameters and execute
	
	$stmt->execute();
	$result= $stmt->get_result();


	//$result = $conn->query($stmt);
	if ($result->num_rows > 0) {
	  // output data of each row
	  while($row = $result->fetch_assoc()) {
		  
		//Check how old the data is
	    $gmdate = date_create();
		$secondsOfTol=3600;
		  
		//Calculating appropriate status description:<br>
		$status="0";
		  
		if($row['time']<(date_timestamp_get($gmdate)-$secondsOfTol)){
			$status="UNREACHABLE";
		}elseif($row['rxstatus']=="0"){
			$status="PINGING - NO POS AV";
		}elseif($row['rxstatus']=="1"){
			$status="NO GPS - LoRa GW";
		}elseif($row['rxstatus']=="1.5"){
			$status="NO GPS - LoRa TRI-EXT POS";
		}elseif($row['rxstatus']=="2"){
			$status="GPS LOCK - EXACT POS";
		}else{
			$status="ERROR";
		}
		  
		//TIME ZONE:
		//$lastrxtimestamp=strtotime($row['rxlast'])+$_SESSION['timezone']*3600; //Deprecated since Nov 1st,2023
		  
		  
		$arrayResult = array(
			"deviceid" => $row['deviceid'],
			"time" => $row['time'],
			"lat" => $row['lat'],
			"lng" => $row['lng'],
			"altitude" => $row['altitude'],
			"speed" => $row['speed'],
			"degrees" => $row['degrees'],
			"dir" => $row['dir'],
			"status" => $status,
			"timedata" => $row['timedata'],
			"txcounter" => $row['txcounter'],
			"rxcounter" => $row['rxcounter'],
			"satnum" => $row['satnum'],
			"txperiodmins" => $row['txperiodmins'],
			"voltageBattery" => $row['voltageBattery'],
			"voltageSolar" => $row['voltageSolar'],
			"charging" => $row['charging'],
			//"debug" => date_timestamp_get($gmdate),
			//"rxlast" => date("Y-m-d H:i:s", $lastrxtimestamp), //Deprecated since Nov 1st,2023
			"currtime"=> date_timestamp_get($gmdate)
		);
		  
		if(isset($_SESSION['devices'])&& in_array($_POST['device'],$_SESSION['devices'])) echo json_encode($arrayResult);
		else echo "403";
		//http_response_code(403);
	  }
	} else {
	  echo "0 results";
	}

	$stmt->close();
	//$conn->close();


	
?>

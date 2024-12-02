<?php header("Access-Control-Allow-Origin: *"); ?>
<?php


	header("Access-Control-Allow-Origin: *");
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: *');
	header('Content-Type: application/json');
/*
	require 'vendor/autoload.php';
	use Spatie\Async\Pool;

	$pool = Pool::create();
*/
	$reqcont=file_get_contents('php://input');

	//Save $reqcont in a file

	$logTTN= fopen('logTTNNov21Night.txt','a');
	fwrite($logTTN,$reqcont.',');
	fclose($logTTN);



	
    
    $reqrec=json_decode($reqcont, true);
	$dataPL=$reqrec['uplink_message']['decoded_payload'];
	$csvString = $dataPL['did'] . "," . $dataPL['tmTTN'] . "," . $dataPL['tmdata'] . "," . $dataPL['lat'] . "," . $dataPL['lng'] . "," . $dataPL['alt'] . "," . $dataPL['spd'] . "," . $dataPL['deg'] . "," . $dataPL['dir'] . "," . $dataPL['satNum'] . "," . $dataPL['rxs'] . "," . $dataPL['voltageBattery'] . "," . $dataPL['voltageSolar'] . "," . $dataPL['txCount'] . "," . $dataPL['rxCount'] . "," . $dataPL['txPeriodMinutes'] . "," . $dataPL['charging'];

	$logTTNCSV= fopen('logCSV.csv','a');
	fwrite($logTTNCSV,$csvString."\r\n");
	fclose($logTTNCSV);
	
	if(isset($reqrec['uplink_message']['decoded_payload']['did'])){
		$dbUser="wherethefuckismydevice";
		$dbPsw="8@cEn4dn7yzxrEbQ3";
		$dbName="my_wherethefuckismydevice";
		$dbAddress="localhost";
		// Create connection
		$conn = new mysqli($dbAddress, $dbUser, $dbPsw, $dbName);


		// Check connection
		if ($conn->connect_error) {
			echo "300";
		  die("Connection failed: " . $conn->connect_error);
			
		}
		
		// SIGNAL GUIDE:
		/*
		CODE 1 RXS: Signal TTN Has position invalid. Since it is not possible to know from TTN if the Gateways have no position (Case 0), some
		position (Case 1) or Sufficient positions to do trianglulation (Case 1.5) we will differentiate these cases here.<br>

		CODE 2 RXS: Position is indeed within valid ranges of GPS, hence it is valid and can be used.				
		
		*/
		
		// Obtaining Actual Coordinates - Either from GPS Sensor, or Calculated Position from ML or (last chance) from GW Only
		$loc_lat=$loc_lng=0.0;
		
		// Fetching if unavailable GPS - 1 from GW, 1.5 from Machine-Learning
		if($reqrec['uplink_message']['decoded_payload']['rxs']==1){

			
			# Let's check if the case is of 1.5 ( Lora Triangulation)
			$GWCount=0;
			$gwObtained=array();
			// This is for the rx_time to make difference from
			$datetimeelem= new DateTime($reqrec['uplink_message']['received_at']);
			foreach($reqrec['uplink_message']['rx_metadata'] as $GW){
				
				
				if ( isset($GW['location']) && isset($GW['rssi']) && isset($GW['snr']) ) {

					$latGW = $GW['location']['latitude'];
					$lngGW = $GW['location']['longitude'];
					//$currentDist=distance($latTUD, $lngTUD, $latGW, $lngGW, "K");


					$rssiGW=$GW['rssi'];
					$snrGW=$GW['snr'];
					$rxTimeGw=new DateTime($GW['received_at']);
					
					$timeDifference=$datetimeelem->diff($rxTimeGw);
					$timeReq=($timeDifference->f)*1000000000;

					$gwObtained[]= array('rssi' => $rssiGW, 'snr' => $snrGW, 'timereq' => $timeReq, 'lat'=>$latGW , 'lng'=>$lngGW);

					$GWCount++;


				}
			
				if ($GWCount==3) break;
			}
			
			// CASE 1 Or 0: Not sufficient GW For tracking but one has pos		
			if ($GWCount!=0 && $GWCount<=2){
				echo "CASE 1 GW1 FOUND";
				// This gets from the one it auto detected. This could be wrong -> fetch it from the altitude ones
				$loc_lat=$gwObtained[0]['lat'];
				$loc_lng=$gwObtained[0]['lng'];
				
			}
			
			// CASE 1.5: Subject Found GW Position to ML Alg
			elseif ($GWCount>2) {
				echo "CASE GW2+ FOUND";
				try {
					$url = "http://83.84.21.31/predictor.php";
					$url = "http://66.45.225.235:6969/";
					$data = json_encode($gwObtained);
					echo $data;
					$options = array(
						'http' => array(
							'header'  => "Content-type: application/json\r\n",
							'method'  => 'POST',
							'content' => $data
						)
					);
					$context  = stream_context_create($options);
					$results=file_get_contents($url, false, $context);
					echo "\n".$results."\n";
					$result=json_decode($results,true);
					var_dump($result);
					if (isset($result['lat']) && isset($result['lng']) ){
						// CASE 1.5 Confirmed
						echo "CASE 1.5 OK";
						var_dump($result);
						$loc_lat=$result['lat'];
						$loc_lng=$result['lng'];
						$reqrec['uplink_message']['decoded_payload']['rxs']=1.5;
						
					}
				} catch (Exception $e) {
					echo "CASE EXC PREDICTION ERR";
					$loc_lat=666.0;
					$loc_lng=666.0;
					echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
				

			}else{
				// CASE 0: No sufficient element to find such position
				$loc_lat=666.0;
				$loc_lng=666.0;
			}

			
			//echo "DUMP";
			//var_dump($reqrec['uplink_message']['rx_metadata']);
		}
		
		else if ($reqrec['uplink_message']['decoded_payload']['rxs']==2) {
			$loc_lat=$reqrec['uplink_message']['decoded_payload']['lat'];	
			$loc_lng=$reqrec['uplink_message']['decoded_payload']['lng'];
		}else{
			// CASE 0: No Location from GPS - No LoRa - Only pain and suffering e.g device just pinged to ttn
			$loc_lat=666.0;
			$loc_lng=666.0;
		}
		
		// MOVED THESE ON TTN
		/*
		if($reqrec['uplink_message']['decoded_payload']['alt']<-200 || $reqrec['uplink_message']['decoded_payload']['alt']>20000 ){
			$reqrec['uplink_message']['decoded_payload']['alt']=-666.0;
		}
		if($reqrec['uplink_message']['decoded_payload']['spd']<-200 || $reqrec['uplink_message']['decoded_payload']['spd']>20000 ){
			$reqrec['uplink_message']['decoded_payload']['spd']=-666.0;
		}
		*/

		// PREPARED STATEMENT BINDING - GPS Version ( CASE 2)
		echo "CASE:".$reqrec['uplink_message']['decoded_payload']['rxs']."\n";
		if($reqrec['uplink_message']['decoded_payload']['rxs']==2){
			$stmt = $conn->prepare("UPDATE devices SET devicename=?, time=?,lat=?,lng=?,altitude=?,speed=?,degrees=?,dir=?,rxstatus=?,timedata=?, txcounter=?, rxcounter=?, satnum=?,txperiodmins=?,voltageBattery=?,voltageSolar=?, charging=? WHERE deviceid=?");

			$stmt->bind_param("sidddddsiiiiiiiiii",$reqrec['end_device_ids']['device_id'],$reqrec['uplink_message']['decoded_payload']['tmTTN'], $loc_lat, $loc_lng, $reqrec['uplink_message']['decoded_payload']['alt'],$reqrec['uplink_message']['decoded_payload']['spd'],$reqrec['uplink_message']['decoded_payload']['deg'],$reqrec['uplink_message']['decoded_payload']['dir'],$reqrec['uplink_message']['decoded_payload']['rxs'],$reqrec['uplink_message']['decoded_payload']['tmdata'], $reqrec['uplink_message']['decoded_payload']['txCount'],  $reqrec['uplink_message']['decoded_payload']['rxCount'],$reqrec['uplink_message']['decoded_payload']['satNum'],  $reqrec['uplink_message']['decoded_payload']['txPeriodMinutes'], $reqrec['uplink_message']['decoded_payload']['voltageBattery'],$reqrec['uplink_message']['decoded_payload']['voltageSolar'], $reqrec['uplink_message']['decoded_payload']['charging'],$reqrec['uplink_message']['decoded_payload']['did'] );
		}else{
			// PREPARED STATEMENT BINDING - NO GPS Lock Version but got ping or else (Case 1, 1.5)
			$stmt = $conn->prepare("UPDATE devices SET devicename=?,time=?,lat=?,lng=?,altitude=?,speed=?,degrees=?,dir=?,rxstatus=?, txcounter=?, rxcounter=?, satnum=?,txperiodmins=?,voltageBattery=?,voltageSolar=?, charging=?  WHERE deviceid=?");
			
			if($loc_lat == 666.0 ||  $loc_lng == 666.0){
				// Here case 0 Is introduced
				$reqrec['uplink_message']['decoded_payload']['rxs']=0;
			}
			$stmt->bind_param("sidddddsiiiiiiiii",$reqrec['end_device_ids']['device_id'],$reqrec['uplink_message']['decoded_payload']['tmTTN'], $loc_lat, $loc_lng, $reqrec['uplink_message']['decoded_payload']['alt'],$reqrec['uplink_message']['decoded_payload']['spd'],$reqrec['uplink_message']['decoded_payload']['deg'],$reqrec['uplink_message']['decoded_payload']['dir'],$reqrec['uplink_message']['decoded_payload']['rxs'], $reqrec['uplink_message']['decoded_payload']['txCount'],  $reqrec['uplink_message']['decoded_payload']['rxCount'],  $reqrec['uplink_message']['decoded_payload']['satNum'],  $reqrec['uplink_message']['decoded_payload']['txPeriodMinutes'], $reqrec['uplink_message']['decoded_payload']['voltageBattery'],$reqrec['uplink_message']['decoded_payload']['voltageSolar'], $reqrec['uplink_message']['decoded_payload']['charging'],$reqrec['uplink_message']['decoded_payload']['did']);
			
		}
		
		//Params of prepared statement
		if($stmt==false){echo "(ERROR)";}
		else $stmt->execute();

		echo "200,timestamp sent:".$reqrec['uplink_message']['decoded_payload']['tmTTN'];
		echo $stmt->error;

		$stmt->close();
		
		// PREPARE CSV INQUIRY
		
		$val=true;
		$stmt = $conn->prepare("SELECT * FROM csvfiles WHERE beingtracked=? AND deviceid=?");
    	$stmt->bind_param("dd", $val, $reqrec['uplink_message']['decoded_payload']['did']);
		//Params of prepared statement
		if($stmt==false){echo "(ERROR CSV)";}
		else{
			$stmt->execute();
			$result= $stmt->get_result();
				if ($result->num_rows > 0) {
				  include "./csv.php";
				  // For each CSV File ready
				  $header = array( 'Device Id','Time Received','Time Gps Data','Latitude','Longitude','Altitude','Speed','Degrees','Direction','Number of Satellites','Status','Battery Voltage','Solar Panel Voltage','Tx Counter','Rx Counter','Sending Period (Mins)', 'Charging status');
				  while($row = $result->fetch_assoc()) {
					  
					  if($loc_lat == 666.0 || $loc_lng == 666.0){
						  $loc_lat="";
						  $loc_lng="";
					  }
					  
					  
					  $csvFile=new csvFile( './csv/'.$row['filename'], 'a', $header );
					  $csvEntry= array($reqrec['uplink_message']['decoded_payload']['did'],$reqrec['uplink_message']['decoded_payload']['tmTTN'],$reqrec['uplink_message']['decoded_payload']['tmdata'], $loc_lat, $loc_lng, $reqrec['uplink_message']['decoded_payload']['alt'],$reqrec['uplink_message']['decoded_payload']['spd'],$reqrec['uplink_message']['decoded_payload']['deg'],$reqrec['uplink_message']['decoded_payload']['dir'],$reqrec['uplink_message']['decoded_payload']['satNum'],$reqrec['uplink_message']['decoded_payload']['rxs'],$reqrec['uplink_message']['decoded_payload']['voltageBattery'],$reqrec['uplink_message']['decoded_payload']['voltageSolar'],$reqrec['uplink_message']['decoded_payload']['txCount'],$reqrec['uplink_message']['decoded_payload']['rxCount'],$reqrec['uplink_message']['decoded_payload']['txPeriodMinutes'], $reqrec['uplink_message']['decoded_payload']['charging']);
					  $csvFile->write($csvEntry);

					  
				  }
				}
			
		}
		
		
		$stmt->close();
		
		$conn->close();
		
	}else{
		echo "403 " + $reqrec['uplink_message']['decoded_payload']['did'];
		http_response_code(403);
	}
?>
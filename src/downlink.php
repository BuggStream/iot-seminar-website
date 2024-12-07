<?php

session_set_cookie_params(86400); 
session_start();


if(!isset($_SESSION['Username'])){
	echo "{\"status\": \"notAllowed\"}";
	return;
}

$devicename=" ";
$mins=30;

/* Checks if i'm entitled to do anything with this device*/

if(isset($_POST['device']) && isset($_POST['minutes']) && isset($_SESSION['devices']) && in_array($_POST['device'],$_SESSION['devices'])){
	try{
		/*Attempt to contact database and retrieve EUI Id (devicename) associated with deviceid*/
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
		$stmt = $conn->prepare("SELECT * FROM devices WHERE deviceid=? ");
		$deviceid= $_POST['device'];
		$stmt->bind_param("i", $deviceid);

		// set parameters and execute

		$stmt->execute();
		$result= $stmt->get_result();


		if ($result->num_rows ==1 ) {
			while($row = $result->fetch_assoc()) {
				$devicename = $row['devicename'];
			}
		}else{
			/*In theory, none should get here*/
			echo "{\"status\": \"error\"}";
			return;
		}
	
	}catch(Exception $e){
		echo "{\"status\": \"errorConn\"}";
		return;
	}
	
}elseif(!isset($_SESSION['devices']) ){
	/*In theory, none should get here*/
	echo "{\"status\": \"notAllowed\"}";
	return;
}elseif(!isset($_POST['device']) || !isset($_POST['minutes']) ){
	/*In theory, idiots should get here*/
	echo "{\"status\": \"emptyRequest\"}";
	return;
}else{
	/* Return devices list - usually when logged but have not that specifice device in session or simply stuff is missing*/
	//http_response_code(403);
	echo "{\"status\": \"notAllowed\", \"deviceid\": ".$_POST['device'].", \"available\": ".json_encode($_SESSION['devices'])."}";
	return;
}

try{
	$url="https://eu1.cloud.thethings.network/api/v3/as/applications/loragpstrack/webhooks/gps-test-website/devices/".$devicename."/down/replace";
	$mins=$_POST['minutes'];
	
	$apiKey="NNSXS.UFQPULLWG65MSWUO3TLZ2HJQQKX2LHWB6AXTKSI.VQ2DOD3YBWXFAHAVJ2ZIM6CRNHOHRPJGUEBIGTOVGR2DIUXYTCSQ";
	
	$payload_device = array(0xd7, 0x34, 0x81, $mins);
	//$payload_device_hex = array_map('dechex', $payload_device);
	//$payload_device_string = implode($payload_device_hex);
	//$payload_device_string = implode(array_map("chr", $payload_device));
    $encoded_payload = base64_encode(call_user_func_array('pack', array_merge(array('C*'), $payload_device)));
	$downlink = array("frm_payload"=>$encoded_payload, "f_port"=>2 ,"schedule"=>"replace","priority"=>"NORMAL", "confirmed"=>true);
	
	
	$payload_ttn=array("downlinks"=>array($downlink) );
	$JSON_payload_ttn=json_encode($payload_ttn);
	//echo $JSON_payload_ttn;
	$options = array(
		'http' => array(
			//'header'  => json_encode(array("Content-type"=>"application/json\r\n", 'Authorization'=> 'Bearer '.$apiKey)),
			'method'  => 'POST',
            'header' => 'Content-type: application/json' . "\r\n" .
                        'Authorization: Bearer ' . $apiKey. "\r\n".
						'User-Agent: my-integration/my-integration-version',
			'content' => $JSON_payload_ttn
		)
	);
	$context  = stream_context_create($options);
	$results=file_get_contents($url, false, $context);
	echo "\n RESULT: ".$results."\n";
	$result=json_decode($results,true);
	//var_dump($result);
	echo "{\"status\": \"ok\"}";
	return;
	
}catch (Exception $e){
	 //echo 'Caught exception: ',  $e->getMessage(), "\n";
	echo "{\"status\": \"errorConn\"}";
	return;
}

?>
<?php
session_set_cookie_params(86400); 
session_start();
$header = array( 'Device Id','Time Received','Time Gps Data','Latitude','Longitude','Altitude','Speed','Degrees','Direction','Number of Satellites','Status','Battery Voltage','Solar Panel Voltage','Tx Counter','Rx Counter','Sending Period (Mins)', 'Charging status');
header('Content-Type: application/x-www-form-urlencoded');
include "./csv.php";

$user=$_SESSION['Username'];
if(!isset($_SESSION['Username'])){
	echo 0;
	return;
}
#$csvfile=fopen("./csv/track.csv","r");
if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="start" && isset($_REQUEST['deviceid'])){
	/* We create the csv file*/
	$filename=$_REQUEST['file'];
	$filepath='./csv/'.$filename;
	$csvfile = new csvFile( $filepath, 'x', $header);
	/* We inform the database*/
	
	if($csvfile!=false){
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
		
		$user=$_SESSION['Username'];
		$val=true;
		
		$stmt = $conn->prepare("INSERT INTO csvfiles (filename,deviceid,useravailable,beingtracked) VALUES (?,?,?,?)");

		$stmt->bind_param("sisi", $filename, $_REQUEST['deviceid'], $user, $val);

		try{
			$stmt->execute();
		
		}
		catch (Exception $e) {
			echo $e;
			return;
		}
		$stmt->close();
		
		echo 1;
		
	}
	else echo 0;
}elseif(isset($_REQUEST['mode']) && $_REQUEST['mode']=="stop" && isset($_REQUEST['deviceid'])){
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
	
	$stmt = $conn->prepare("UPDATE csvfiles SET beingtracked=0 WHERE useravailable=? AND deviceid=?");
	$stmt->bind_param("sd", $user, $_REQUEST['deviceid']);
	

    $stmt->execute();
}elseif(isset($_REQUEST['mode']) && $_REQUEST['mode']=="check" && isset($_REQUEST['deviceid']) ){
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

	$val=true;
	$stmt = $conn->prepare("SELECT * FROM csvfiles WHERE useravailable=? AND beingtracked=? AND deviceid=?");
    $stmt->bind_param("sdd", $user, $val, $_REQUEST['deviceid']);
	

    $stmt->execute();
	$result = $stmt->get_result();
	//var_dump($result);
	if ($result->num_rows > 0) {
		echo 1;
	}else{
		echo 0;
	}
}elseif(isset($_REQUEST['mode']) && $_REQUEST['mode']=="list"){
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

	$stmt = $conn->prepare("SELECT * FROM csvfiles WHERE useravailable=?");
    $stmt->bind_param("s", $user);
	

    $stmt->execute();
	$result = $stmt->get_result();
	//var_dump($result);
	$fileList=array();

	if ($result->num_rows > 0) {
	  while($row = $result->fetch_assoc()) {
		  array_push($fileList,$row['filename']);
		  
	  }
	}
	
	echo json_encode($fileList);
}else{
	//var_dump($_REQUEST);
	$filename=$_REQUEST['file'];
	/* GET USER -> GET TABLES -> RETURN TABLE*/	
	$csvfile=new csvFile( './csv/'.$filename, 'r', $header );
	//echo $csvfile;

	$csvfile->readFile();
	/*
	while($row=fgetcsv($csvfile->handle,null,';')!= false){
		$num=count($row);
		echo $num;
		/*
		foreach($row as $value){
			echo $value;
		}<br>

		for ($c=0; $c < $num; $c++) {
			echo $row[$c] . "<br />\n";
		}

	}
	*/

}


?>
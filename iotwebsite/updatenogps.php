<?php header("Access-Control-Allow-Origin: *"); ?>
<?php


	header("Access-Control-Allow-Origin: *");
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: *');
	header('Content-Type: application/json');
	$reqcont=file_get_contents('php://input');
    
    $logTTN= fopen('NewTTNFalconLog.txt','a');
	fwrite($logTTN,$reqcont.",\n");
	fclose($logTTN);
?>
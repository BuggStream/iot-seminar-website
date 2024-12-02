
<?php
//echo "Predictor Start:\n";
require 'vendor/autoload.php';
use Spatie\Async\Pool;

$pool = Pool::create();


//$pool[] = async( function() {
	
	$gw1=array('rssi' => -85, 'snr' => 11.5, 'timereq' => -2132579, 'lat'=>51.99599268009992 , 'lng'=>4.3836039304733285);
	$gw2=array('rssi' => -117, 'snr' => -15.25, 'timereq' => 135204847, 'lat'=>52.060628073287646 , 'lng'=>4.402459859848023);
	$gw3=array('rssi' => -123, 'snr' => -5.2, 'timereq' => -632984, 'lat'=>51.98687557914596 , 'lng'=>4.367481172084809);
	
	$gwData=array($gw1,$gw2,$gw3);
	//$url = "http://83.84.21.31/predictor.php";
	$url = "http://66.45.225.235:6969/";
	$data = json_encode($gwData);
	echo $data;
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/json\r\n",
			'method'  => 'POST',
			'content' => $data
		)
	);
	$context  = stream_context_create($options);
	return file_get_contents($url, false, $context);
							
							//})->then(function($result){echo "Done"; echo $result;});
//$loop->then(function(){echo $result;});
//$pool [] = async ( function(){echo "ASYNC PRECEDES";});
await($pool);
//echo "AABBCC";

//$result=file_get_contents("http://83.84.21.31/predictor.php");
/*
$curlSES=curl_init();
curl_setopt($curlSES,CURLOPT_URL,"http://83.84.21.31/predictor.php");
curl_setopt($curlSES,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curlSES,CURLOPT_HEADER, false); 
//step3
$result=curl_exec($curlSES);
//step4
curl_close($curlSES);
//step5
*/
echo $result;
//echo "\nREQUESTED\n";

/*
require_once './vendor/autoload.php';

use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\ModelManager;

$filepath ='model';
$modelManager = new ModelManager();
$regressor = $modelManager->restoreFromFile($filepath);

echo "\nCOMPLETED\n";
echo $regressor->predict([-121,-1.8]);
*/
	
?>
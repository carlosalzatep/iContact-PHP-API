<?php
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);

// Load the iContact library
require_once('lib/iContactApi.php');

// Give the API your information
iContactApi::getInstance()->setConfig(array(
	'appId'       => 'xxxxx', 
	'apiPassword' => 'xxxxx', 
	'apiUsername' => 'xxxxx'
));

// Store the singleton
$oiContact = iContactApi::getInstance();
// Try to make the call(s)
try {

	date_default_timezone_set('America/Bogota'); 
	$hostname_conectaBD = "xxxxx";
	$database_conectaBD = "xxxxx";
	$username_conectaBD = "xxxxx";
	$password_conectaBD = 'xxxxx';

	//Conexión BD MySQLi
	$db = $mysqli = new mysqli($hostname_conectaBD, $username_conectaBD, $password_conectaBD, $database_conectaBD);
	if ($mysqli->connect_errno) {
		echo "Error conexión!";
	}

	function getAllAccountSends($db, $AccountId, $AccountName, $Respuesta, $oiContact){
	
		foreach ($Respuesta->sends as $sendID) {
			//Subject
			$messajeInfo = $oiContact->getMessagesInfo($sendID->messageId, $AccountId);

			//Statistics
			$messajeStats = $oiContact->getMessagesStatistics($sendID->messageId, $AccountId);

			$messageId= $sendID->messageId;
			$status= $sendID->status;
			$releasedTime= $sendID->releasedTime;
			$subject= $messajeInfo->message->subject;
			$sent= $messajeStats->statistics->sent;
			$bounces=$messajeStats->statistics->bounces;
			$delivered= $messajeStats->statistics->delivered;
			$unsubscribes= $messajeStats->statistics->unsubscribes;
			$opensUnique= $messajeStats->statistics->opens->unique;
			$opensTotal= $messajeStats->statistics->opens->total;
			$clicksUnique= $messajeStats->statistics->clicks->unique;
			$clicksTotal= $messajeStats->statistics->clicks->total;
			

			$SQL = utf8_decode("
			INSERT INTO icontact_datastudio (`messageId`, `AccountName`, `AccountId`, `status`, `releasedTime`, `subject`, `sent`, `bounces`, `delivered`, `unsubscribes`, `opensUnique`, `opensTotal`, `clicksUnique`, `clicksTotal`) 
			VALUES ('$messageId', '$AccountName', '$AccountId', '$status', '$releasedTime', '$subject', '$sent', '$bounces', '$delivered', '$unsubscribes', '$opensUnique', '$opensTotal', '$clicksUnique', '$clicksTotal');
			");
			
			$db->query($SQL);
			//var_dump($MessageData);
			echo "<hr>";
		}
	}

	// Get Send messages
	$AccountId = xxxxx;
	$AccountName = "xxxxx"; 
	$Respuesta = $oiContact->getSendMessages(null, $AccountId);
	getAllAccountSends($db, $AccountId, $AccountName, $Respuesta, $oiContact);

	$db->close();
	
} catch (Exception $oException) { // Catch any exceptions
	// Dump errors
	var_dump($oiContact->getErrors());
	// Grab the last raw request data
	var_dump($oiContact->getLastRequest());
	// Grab the last raw response data
	var_dump($oiContact->getLastResponse());
}

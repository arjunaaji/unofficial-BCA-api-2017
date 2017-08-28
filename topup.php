<?php
date_default_timezone_set("Asia/Jakarta");
	$main_url = 'https://api.finhacks.id'; 
	$client_id = '15490d9d-5943-40b7-b334-37daa73526e5'; 
	$client_secret = 'bf9de6ab-66f5-430c-87d0-ffa5a0396334'; 
	$api_key = '34f3e930-a62a-4f61-bb35-b47ce88ec82f'; 
	$api_secret = '0112592e-83f8-494c-adab-2a3ab81e29b7';
	$access_token = null;
	$signature = null;
	$timestamp = null;
	$corporate_id = '88856'; 
	$account_number = '8220001394'; 

	$path = '/api/oauth/token';	
	$data = array(
		'grant_type' => 'client_credentials'
	);
	$headers = array(
		'Content-Type: application/x-www-form-urlencoded',
		'Authorization: Basic '.base64_encode($client_id.':'.$client_secret));
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $main_url.$path);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore Verify SSL Certificate
	curl_setopt($ch, CURLOPT_POST, true); // Ignore Verify SSL Certificate
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Ignore Verify SSL Certificate
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Ignore Verify SSL Certificate
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Ignore Verify SSL Certificate
	curl_setopt($ch, CURLOPT_SSLVERSION, 6);
	
	$output = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($output,true);
	$access_token = $result['access_token'];
	//echo "\ntoken:".$access_token;
	
	$timestamp = date(DateTime::ISO8601);
	$timestamp = str_replace('+','.000+', $timestamp);
	$timestamp = substr($timestamp, 0,(strlen($timestamp) - 2));
	$timestamp .= ':00';
	
	$nohp=$_GET['nohp'];
	$nominal=$_GET['nominal'];
	
	$data=array (
    "CompanyCode" => "88856",
    "PrimaryID" => "$nohp",
    "TransactionID"=> "TRX12345678".rand(0, 9).date(His),
    "RequestDate" => $timestamp, 
    "Amount" => $nominal.".00",
    "CurrencyCode" => "IDR"
	);
	
	$dataEncode = json_encode($data);
	$method='POST';
	$path='/ewallet/topup';
	$encryptedData = (empty($dataEncode)) ? '' : strtolower(hash('sha256', $dataEncode));	
	$signature = hash_hmac('sha256', $method.":".$path.":".$access_token.":".$encryptedData.":".$timestamp, $api_secret);
	
	
	
	$headers = array(
		'Authorization: Bearer '.$access_token,
		'Content-Type: application/json',
		'Origin: mainkode.com',
		"X-BCA-Key: ".$api_key,
		"X-BCA-Timestamp: ".$timestamp,
		"X-BCA-Signature: ".$signature		
		);
		
	//echo "\nURL:".$main_url.$path;	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $main_url.$path);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_POST, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $dataEncode); 
	curl_setopt($ch, CURLOPT_SSLVERSION, 6);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$output = curl_exec($ch);
	curl_close($ch);
	echo $output;
	//echo "\n".$httpcode;
	

?>
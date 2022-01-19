<?php
	
	// Change these variables
	$url = 'tracedock.php';
	$key = '[LIGHTSPEED_API_KEY]';
	$secret = '[LIGHTSPEED_API_SECRET]';
	// End of variables
	
	$curl = curl_init();
	
	$postfields = [
		'webhook' => [
			'isActive' => true,
			'itemGroup' => 'orders',
			'itemAction' => 'paid',
			'language' => 'nl',
			'format' => 'json',
			'address' => $url,
		]
	];
	
	$postfields = http_build_query($postfields);
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://api.webshopapp.com/nl/webhooks.json',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => $postfields,
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Basic '. base64_encode($key . ":" . $secret),
		'Content-Type: application/x-www-form-urlencoded'
	  ),
	));
	
	$response = curl_exec($curl);
	
	curl_close($curl);
	echo $response;

?>
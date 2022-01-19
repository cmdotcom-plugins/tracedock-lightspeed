<?php
	
	header("Access-Control-Allow-Origin: *");	
	
	// Change these variables
	$url = '[TRACEDOCK_ENDPOINT_URL]';
	$key = '[LIGHTSPEED_API_KEY]';
	$secret = '[LIGHTSPEED_API_SECRET]';
	// End of variables
	
	$orderId = $_SERVER[HTTP_X_ORDER_ID];
	$tracedock = new Tracedock();
	$tracedock->handle($orderId);
	
	class Tracedock
	{
		
		public function handle($orderId) {
			
			$order = $this->getLsOrder($orderId);
			$this->postTracedock($order);
			
		}
		
		private function getLsOrder($orderId) {
			
			global $key, $secret;
			
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.webshopapp.com/nl/orders/'. $orderId .'.json',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
				'Authorization: Basic '. base64_encode($key . ":" . $secret),
			  ),
			));
			
			$response = curl_exec($curl);
			
			curl_close($curl);
			
			$arrayResponse = json_decode($response, true);
			return $arrayResponse['order'];
			
		}
		
		private function postTracedock($order) {
			
			global $url;
			
			$products = [];
			foreach($order['products']['resource']['embedded'] as $index => $product) {
				$products[$index] = [
					'id' => $product['id'],
					'name' => $product['productTitle'],
					'price' => $product['priceExcl'],
					'quantity' => $product['quantityOrdered'],
					'brand' => $product['brandTitle'] ? $product['brandTitle'] : '',
					'category' => '',
					'coupon' => $order['discountCouponCode'] ? $order['discountCouponCode'] : '',
					'variant' => $product['variantTitle'] ? $product['variantTitle'] : '',
				];
			}
			
			$tax = 0;
			foreach($order['taxRates'] as $taxRate) {
				$tax += $taxRate['amount'];
			}

            // Lightspeed does not contain a default userId,  as such we use the quoteId to stitch with the browser session.
            // For compatibility with the default templates in TraceDock we will forward this values both as userId and quoteId.
			$data = [
                'userId' => $order['quote']['resource']['id'],
                'quoteId' => $order['quote']['resource']['id'],
				'orderId' => $order['number'],
				'transaction_revenue' => $order['priceExcl'],
				'products' => $products,
				'transaction_shipping' => $order['shipmentPriceExcl'],
				'transaction_tax' => number_format($tax, 2, '.', '')
			];
			
			$curl = curl_init();
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => json_encode($data),
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			  ),
			));
			
			$response = curl_exec($curl);
			
			curl_close($curl);
			echo $response;
			
		}
		
	}
	
?>
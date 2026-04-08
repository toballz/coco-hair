<?php
$isTestMode = true;



class tools
{

	//
	public static function generateRandomAlphanumeric($length)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	//  Custom send SMS
	public static function custom_send_sms($numberCode, $phonenumber, $messager)
	{
		$url = "https://sms-gateway.q1-site.site/api/core/v1/pushaddNewMessage";
		$data = [
			"phonenumber" => $phonenumber,
			"country" => "united states",
			"shortcountry" => "us",
			"countryphonecode" => $numberCode,
			"message" => $messager
		];


		$ch = curl_init($url);
		if ($ch === false) {
			throw new Exception("cURL init failed");
		}


		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Content-Type: application/json",
			"Content-Length: " . strlen(json_encode($data))
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($response === false || $httpCode !== 200) {
			return false;
		} else {
			return true;
		}
	}

	public static function stripe_Create_Dynamic_Link_for_payments($cemail, $pprice, $orderID4, $customerName)
	{
		global $isTestMode;
		
		if (!isset($cemail) || !isset($pprice)) {
			exit("Payment platform error #2896-2407");
		}
		if (strlen($cemail) < 4 || !filter_var($cemail, FILTER_VALIDATE_EMAIL) || $pprice < 1) {
			exit("Payment platform error #1352-3745");
		}
		if (strlen($orderID4) < 2) {
			exit("Payment platform error #4890-7455");
		}
		$realpprice = $pprice * 100;
		//
		require_once dirr . '/3rdparty/stripe-php-master/init.php';
		//
		\Stripe\Stripe::setApiKey(Env::$STRIPE_API_KEY);
		//
		//
		//create customer
		$thisCustomer = \Stripe\Customer::create([
			'email' => $cemail,
			'name' => $customerName . ' - ' . $orderID4
		]);
		//
		// Create a new Checkout Session
		$session = \Stripe\Checkout\Session::create([
			'customer' => $thisCustomer->id,
			'payment_method_types' => ['card'],
			'line_items' => [
				[
					'price_data' => [
						'currency' => 'usd',
						'product_data' => [
							'name' => site::name,
						],
						'unit_amount' => $realpprice, // Amount in cents
					],
					'quantity' => 1,
				],
			],
			'payment_intent_data' => [
				'setup_future_usage' => 'off_session',
				'metadata' => [
					'orderID' => $orderID4, // Include order ID in metadata
					'orderEmail' => $cemail
				],
			],
			'mode' => 'payment',
			'success_url' => site::url_hostdir() . '/pages/receipt.php?redrfrm=stripe&orderId=' . $orderID4,
			'cancel_url' => site::url_hostdir() . '/pages/hairlist.php'
		]);
		return $session->url;
	}
}






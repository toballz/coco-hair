<?php include_once("../config.php");
require_once('./stripe-php-master/init.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function mailCustomer_notifyAdmin($oid, $customerEmsil)
{
    global $db;
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';


    $orderUrl = site::url_hostdir() . "/pages/receipt.php?orderId=" . $oid;
    $alertSMSMessage =
        "A new appointment has been set\r\n" .
        "----------------------------\r\n" .
        $orderUrl;


    $customerReceiptMessage = $orderUrl;

    // 2: get receipt content html for email
    $reciept_path = '../pages/receipt.php';
    if (file_exists($reciept_path)) {
        ob_start();
        $_GET['orderId'] = $oid;
        $_GET['email'] = 1;
        include($reciept_path);
        $customerReceiptMessage = ob_get_clean();
    }

    // 1: receipt html
    $Customer_receiptHtml =
        "Hair Appointment Confirmation | CocoHairSignature\n\n" .
        "<br/><br/>" .
        "<a href='" . $orderUrl . "'>View appointment details on the browser - " . $orderUrl . "</a>
        <br/><br/>" .
        $customerReceiptMessage;
    $Customer_receiptSms = "Hair Appointment Confirmation | CocoHairSignature " . $orderUrl;
    // 0:

    $mail = new PHPMailer(true);
    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = env::STMP_HOST;                     //Set the SMTP server to send through
        $mail->SMTPAuth = env::STMP_AUTH;                                   //Enable SMTP authentication
        $mail->Username = env::STMP_USERNAME;                     //SMTP username
        $mail->Password = env::STMP_PASSWORD;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = env::STMP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('allsupports@cocohairsignature.com', 'Appointment booked');
        $mail->addReplyTo('cocohairsignature@gmail.com');
        $mail->addAddress(trim($customerEmsil));     //Add a recipient ., customer

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Appointment booked!!';
        $mail->Body = $Customer_receiptHtml;
        $mail->AltBody = $Customer_receiptSms;

        $mail->send();
        echo "email sent.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // notify website owner by sms
    // if (tools::custom_send_sms("1", "2244401819", $alertSMSMessage)) {
    //     echo "\n sms sent";
    // }
    // ## sendd to website developer
    if (tools::custom_send_sms("1", "8506317422", $alertSMSMessage)) {
        echo "\n sms sent";
    }



}

// ("m5624vr0n","beautyfye@icloud.com");



//secret Stripe API key
\Stripe\Stripe::setApiKey(env::STRIPE_SECRET_KEY_API);

// Handle the incoming webhook event
$payload = @file_get_contents('php://input');
$event = null;
try {
    $event = \Stripe\Webhook::constructEvent($payload, $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '', env::STRIPE_SIGNING_SECRET);
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit($e);
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit($e);
}

// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
        //Get metadata from PaymentIntent
        $paymentIntent = $event->data->object; // Contains a Stripe PaymentIntent
        //get orderID 
        $Order_Id = $paymentIntent->metadata->orderID;
        $Order_Email = $paymentIntent->metadata->orderEmail;
        //file_put_contents("./akd.txt", $Order_Email);
        //update if has paid to 1
        $stmt = $db->prepare("UPDATE product_purchased SET haspaid = 1 WHERE id_gen = ?");
        $stmt->execute([$Order_Id]);

        //
        mailCustomer_notifyAdmin($Order_Id, trim($Order_Email));
        break;
    // Handle other event types as needed
}
http_response_code(200);

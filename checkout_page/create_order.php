<?php
require 'vendor/autoload.php'; // Include Razorpay PHP SDK

use Razorpay\Api\Api;

$keyId = 'rzp_test_IQX5JipgAJBxhc'; // Replace with your Razorpay Key ID
$keySecret = 'hTQauYM54E8HG3MATCXuAetX'; // Replace with your Razorpay Key Secret
$api = new Api($keyId, $keySecret);

// Fetch the amount from the request (in paise)
$data = json_decode(file_get_contents('php://input'), true);
$amount = $data['amount']; // Amount in paise

// Create an order
$order = $api->order->create([
    'amount' => $amount,
    'currency' => 'INR',
    'receipt' => 'order_rcptid_' . time(),
    'payment_capture' => 1 // Auto-capture payment
]);

// Return the order ID to the client
echo json_encode(['id' => $order->id, 'amount' => $order->amount]);
?>
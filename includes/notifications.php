<?php
// File: includes/notifications.php
// Version: FINAL - Re-engineered based on your provided code samples.

/**
 * Sends an order confirmation SMS to the customer via bulksmsbd.net GET API.
 * This function is modeled after your examples for maximum compatibility.
 *
 * @param string $phoneNumber The customer's 11-digit phone number (e.g., '017...').
 * @param string $message The message to be sent.
 * @return void
 */
function sendOrderConfirmationSMS($phoneNumber, $message) {
    $apiKey = 'VAiSMlNbSuWe3jx4zfAF';
    $senderId = '8809617627215';

    // 1. Format the phone number correctly
    if (strlen($phoneNumber) == 11 && strpos($phoneNumber, '01') === 0) {
        $formattedNumber = '88' . $phoneNumber;
    } elseif (strlen($phoneNumber) == 13 && strpos($phoneNumber, '8801') === 0) {
        $formattedNumber = $phoneNumber;
    } else {
        error_log("SMS failed: Invalid phone number format provided: " . $phoneNumber);
        return; // Stop execution if number is invalid
    }

    // 2. URL-encode the message to handle special characters safely
    $encodedMessage = urlencode($message);

    // 3. Build the final API URL with all parameters
    $apiUrl = "http://bulksmsbd.net/api/smsapi?api_key={$apiKey}&type=text&number={$formattedNumber}&senderid={$senderId}&message={$encodedMessage}";

    // 4. Use cURL to send the GET request
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10, // Wait a maximum of 10 seconds for a response
        CURLOPT_CONNECTTIMEOUT => 5 // Wait a maximum of 5 seconds to connect
    ]);

    $response = curl_exec($ch);
    
    // 5. Log the response for debugging
    if (curl_errno($ch)) {
        error_log('cURL error sending SMS to ' . $formattedNumber . ': ' . curl_error($ch));
    } else {
        error_log("BulkSMSBD Response for {$formattedNumber}: " . $response);
    }
    
    curl_close($ch);
}

/**
 * Sends a notification to Pipedream with a specific JSON structure {value1, value2}.
 *
 * @param string $orderInfo A pre-formatted string containing the main order details.
 * @param string $trxInfo A pre-formatted string for transaction details.
 * @return void
 */
function sendTelegramNotification($orderInfo, $trxInfo) {
    $webhookUrl = 'https://eoxzrfzztfgfgod.m.pipedream.net';
    
    // 1. Prepare data in the required { "value1": "...", "value2": "..." } format
    $data = [
        'value1' => $orderInfo,
        'value2' => $trxInfo
    ];

    // 2. Send the data as a JSON POST request
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $webhookUrl,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true, // Though we don't use the response, it's good practice
        CURLOPT_TIMEOUT => 10
    ]);
    
    curl_exec($ch);
    curl_close($ch);
}
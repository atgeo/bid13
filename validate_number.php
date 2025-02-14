<?php

function isValidPhoneNumber($phone_number, $customer_id, $api_key)
{
    $api_url = "https://rest-ww.telesign.com/v1/phoneid/$phone_number";

    $headers = [
        "Authorization: Basic " . base64_encode("$customer_id:$api_key"),
        "Content-Type: application/json" // Ensures the API correctly interprets the request as JSON
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $data = json_encode([
        "consent" => ["method" => 1],
    ]);

    /* According to Telesign API docs, the method is POST, and this line sends the JSON-encoded data
       as the body of the POST request to the API. */

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        return false; // API request failed
    }

    $data = json_decode($response, true);
    if (!isset($data['phone_type']['description'])) { // Changed to match API docs, which use phone_type.description.
        return false; // Unexpected API response
    }

    $valid_types = ["FIXED_LINE", "MOBILE", "VALID"];
    return in_array(strtoupper($data['phone_type']['description']), $valid_types);
}

// Usage example
$phone_number = "1234567890"; // Replace with actual phone number
$customer_id = "your_customer_id";
$api_key = "your_api_key";
$result = isValidPhoneNumber($phone_number, $customer_id, $api_key);
var_dump($result);

<?php
$url = 'http://127.0.0.1/api/legacy';
// use 127.0.0.1 or test domain
$ch = curl_init('http://sgpm-mechanic.test/api/legacy');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'action' => 'login',
    'username' => 'superadmin',
    'password' => 'password123'
]));
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
}
echo "HTTP Code: $httpcode\n";
echo "Response:\n";
echo $response;

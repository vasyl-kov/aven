<?php
header('Content-Type: application/json');

$ip = $_SERVER['REMOTE_ADDR'];
$response = file_get_contents("http://ip-api.com/json/$ip?fields=countryCode");
echo $response ?: json_encode(['country_code' => 'UA']);

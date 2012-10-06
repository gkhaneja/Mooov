<?php
$data = array("uuid" => "550e8400-e29b-41d4-a716-446655440003" , "latitude" => "55.1300", "longitude" => "44.4453" , "des_latitude" => "33.3455",  "des_longitude" => "55.6789" ,"time" => "23445");
$data_json = json_encode($data);

print_r($data_json);
$ch = curl_init('http://127.0.0.1/UserService/addUser/');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_json))
);

$result = curl_exec($ch);
print_r ("Result-->" . $result);
?>

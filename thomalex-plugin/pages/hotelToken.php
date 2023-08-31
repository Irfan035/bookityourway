<?php 
$urlToken = 'https://rest.resvoyage.com/api/v1/public/token?clientname=Book It Your Way';
// $token_url= add_query_arg($urlToken);
$tokenValue = wp_remote_get($urlToken);
$response_token = json_encode($tokenValue, true);
$formated_token = json_decode($response_token, true);
$new_token = json_decode($formated_token['body'], true)['Token'];
$headers_HS = array(
    'Authorization' => 'Bearer ' . $new_token
);
?>
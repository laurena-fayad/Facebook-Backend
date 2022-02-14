<?php
function createJwt($param){
$headers = ['alg'=>'HS256','typ'=>'JWT'];
$headers_encoded = base64url_encode(json_encode($headers));

//build the payload
$payload = ['id'=>$param];
// $payload = ['sub'=>'1234567890','name'=>'John Doe', 'admin'=>true];
$payload_encoded = base64url_encode(json_encode($payload));

//build the signature
$key = 'secret';
$signature = hash_hmac('sha256',"$headers_encoded.$payload_encoded",$key,true);
$signature_encoded = base64url_encode($signature);

//build and return the token
$token = "$headers_encoded.$payload_encoded.$signature_encoded";
return $token;

}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

?>
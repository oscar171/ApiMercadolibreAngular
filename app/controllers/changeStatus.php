<?php 
session_start();
$data = json_decode(file_get_contents("php://input"));
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);
$params = array('access_token' => $_SESSION['access_token']);
$body = array('status' => $data[0]->status);
$response = $meli->put('/items/'.$data[1]->id, $body, $params);
if($response['httpCode']==200){$result = array('mensaje' => 'success' );}else{$result =array('mensaje' => $response['body']->message );}
echo json_encode($result);
 ?>
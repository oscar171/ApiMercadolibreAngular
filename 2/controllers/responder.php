<?php 
$data = json_decode(file_get_contents("php://input"));
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);
$params = array('access_token' => $_SESSION['access_token']);
$body =  array(	'question_id' => $data[0]->id,
	            'text' => $data[1]->resp);
$response = $meli->post('/answers',$body,$params);

if($response['httpCode']==200)
	{$result = array('mensaje' => 'success' );}
else{$result =array('mensaje' => $response['body']->message );
}
echo json_encode($result);

 ?>


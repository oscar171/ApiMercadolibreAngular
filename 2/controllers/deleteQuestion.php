<?php 
session_start();
$data = json_decode(file_get_contents("php://input"));
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);
$result=$meli->delete('/questions/'.$data->id, array('access_token' => $_SESSION['access_token']));
if($result['httpCode']==200){
$response['mensaje']="success";
}
else
{
$response['mensaje']="error";	
}

echo json_encode($response);
        
 ?>
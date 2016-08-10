<?php 
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);

/*$params = array('access_token' => $_SESSION['access_token']);
$result3 = $meli->get('/orders/1155471341', $params);

echo "<pre>";
print_r($result3);
echo "</pre>";*/
$phone='02429414743';
$sub=substr($phone,0,-9);
if($sub=='02')
{echo "Es telefono de casa";}
else
{echo "es celular";}
echo $sub;
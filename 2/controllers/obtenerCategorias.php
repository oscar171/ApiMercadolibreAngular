<?php 
session_start();
//Aqui van la librearia de mercadolibre
require_once '../libs_php/Mercadilivre/Meli/meli.php';
//Aqui va el archivo confi php
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);
$params = array('access_token' => $_SESSION['access_token']);
$response = $meli->get('/sites/MLV/categories', $params);
echo json_encode($response['body']);
 ?>
<?php

/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* @package: recumperamos los datos del usuario logiado
* 
*/ 
if(!isset($_SESSION)){
session_start();

}
$access= array('accessToken' => $_SESSION['access_token'],
				'firstName'=> $_SESSION['first_name'],
				'lastName'=> $_SESSION['last_name']
				 );


echo json_encode($access);
 ?>
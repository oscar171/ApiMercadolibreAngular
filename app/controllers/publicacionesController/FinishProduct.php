<?php 

/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* @package: Codigo utilizado para cambiar los estatus de las publicaciones en mercadolibre desde la 
*aplicacion
* @var $meli meli: objeto de tipo meli, el cual es un SDK para el manejo de peticiones hacia la api de 
*mercadolibre
* @var $params array : objeto de tipo array que contiene el token de accesso, necesario para poder 
*interactuar con la api de mercadolibre* 
* @var $body array : objeto de tipo array que contiene los campos o atributos que van a ser modificado
* en la publicacion de mercadolibre.
* @var $response json  : objeto de tipo json que la informacion de la publicacion modificada y un 
*mensaje de respuesta de la aplicacion si fue ejecutada con exito la peticion o no
*/
session_start();
$data = json_decode(file_get_contents("php://input"));
require_once '../../libs_php/Mercadilivre/Meli/meli.php';
require_once '../../config.php';
$meli = new Meli(APP_ID, APP_KEY);
$params = array('access_token' => $_SESSION['access_token']);
$body = array('delete' => 'true');
$response = $meli->put('/items/'.$data[1]->id, $body, $params);
($response['httpCode']==200)?$result['mensaje']='success':$result['mensaje']=$response['body']->message;
echo json_encode($result);
?>
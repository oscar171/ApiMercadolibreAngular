<?php 

/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* @package: Codigo utilizado para eliminar una pregunta en especifica  
*aplicacion
* @var $meli meli: objeto de tipo meli, el cual es un SDK para el manejo de peticiones hacia la api de 
*mercadolibre
* @var $params array : objeto de tipo array que contiene el token de accesso, necesario para poder 
*interactuar con la api de mercadolibre* 
* @var $result json  : objeto de tipo json que la informacion de la peticion y un 
*mensaje de respuesta de la aplicacion si fue ejecutada con exito la peticion o no
*/
session_start();
$data = json_decode(file_get_contents("php://input"));
require_once '../../libs_php/Mercadilivre/Meli/meli.php';
require_once '../../config.php';
$meli = new Meli(APP_ID, APP_KEY);
$result=$meli->delete('/questions/'.$data->id, array('access_token' => $_SESSION['access_token']));
($result['httpCode']==200)?$response['mensaje']="success":$response['mensaje']="error";

echo json_encode($response);
        
 ?>
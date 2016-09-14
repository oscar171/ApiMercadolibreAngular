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
*mensaje de respuesta de la api si fue ejecutada con exito la peticion o no
*/
session_start();
$data = json_decode(file_get_contents("php://input"));
require_once '../../libs_php/Mercadilivre/Meli/meli.php';
require_once '../../config.php';

$meli = new Meli('APP_KEY', 'APP_ID');


$params = array('access_token' => $_SESSION['access_token']);

$body = array( 'listing_type_id' => $data[1]->listing_type_id,
		'quantity' => 1,
		'title'=> $data[3]->title,
	        'price'=> $data[2]->price,
			 );

//ejecutamos la peticion a mercadolibre
$response = $meli->post('/items/'.$data[0]->id.'/relist', $body, $params);


//verificamos que la peticion se alla realizado con exito
if ($response['httpCode']==201)
{
        $result=array('mensaje'=>'success',
                    'nuevoId'=>$response['body']->id);
}else
{
($response['httpCode']==0)?$result['mensaje']="error de conexion con mercadolibre":$result['mensaje']=$response['body']->message;	
}

echo json_encode($result);

?>
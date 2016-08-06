<?php 

session_start();
$data = json_decode(file_get_contents("php://input"));
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';

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

        $result=array( 'mensaje'=>'success',
                        'nuevoId'=>$response['body']->id);


}else
{
	 if($response['httpCode']==0)
        {
       $result=array( 'mensaje'=>"error de conexion con mercadolibre"); 
        }
        else{

        $result=array( 'mensaje'=>$response['body']->message);
        }
	
}

echo json_encode($result);

?>
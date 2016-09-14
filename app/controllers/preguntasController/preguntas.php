<?php 

/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* @package: Codigo utilizado para eliminar una pregunta en especifica  del vendedor
*
* @var $meli meli: objeto de tipo meli, el cual es un SDK para el manejo de peticiones hacia la api de 
*mercadolibre
* @var $params array : objeto de tipo array que contiene el token de accesso, necesario para poder 
*interactuar con la api de mercadolibre* 
* @var $result json  : objeto de tipo json que la informacion de la peticion y un 
*mensaje de respuesta de la aplicacion si fue ejecutada con exito la peticion o no
*/
session_start();
require_once '../../libs_php/Mercadilivre/Meli/meli.php';
require_once '../../config.php';
$meli = new Meli(APP_ID, APP_KEY);
//$params: array de parametros que filtran la informacion para hacer la peticion a mercadolibre y traer
//solo los datos que nos interesa obtener
//attributes: los atributos o informacion que nos interezan obtener de mercadolibre
//status: filtra la respuesta con todas las preguntas que esten en ese estatus
//limit: cantidad maxima de preguntas que deseo obtener 
$params = array('access_token' => $_SESSION['access_token'],
                'seller_id' => $_SESSION['userid'],
                'attributes' => 'questions,total',
                'status'=> 'UNANSWERED',
                'limit' => 5);
$result3 = $meli->get('/my/received_questions/search',$params);
//verificamos que la peticion se halla realizado correctamente
if($result3['httpCode']==200)
{
//obtenemos los resultados
$questions=$result3['body']->questions;
$arrayQuestion['total']=$result3['body']->total;
//verificamos que el resultado sea mayor a 0
        if($result3['body']->total!=0)
        {   //obtenemos cada pregunta en el array y consultamos sus valores.
            while (!empty($questions)) 
            {
              $questions2=array_shift($questions);
              $idQuestion=$questions2->id;
              $item=$questions2->item_id;
              $text=$questions2->text;
              $params2 = array('attributes' => 'title');
              $result2 = $meli->get('/items/'.$item, $params2);
              $iteminfo=$result2['body'];
              $element= array(
              'title' => $iteminfo->title,
              'text' => $text,
              'idQuestion'=> $idQuestion);              
              $arrayElement[]=$element;                
            }
        $arrayQuestion['question']=$arrayElement;
        $arrayQuestion['mensaje']='success';
        }
        else
         $arrayQuestion['mensaje']='Nodata';
                            
}
else
{ 
 ($result3['httpCode']==0)?$arrayQuestion['mensaje']='Error al conectarse a mercadolibre':$arrayQuestion['mensaje']= $result3['body']->message;
}
echo json_encode($arrayQuestion);

 ?>
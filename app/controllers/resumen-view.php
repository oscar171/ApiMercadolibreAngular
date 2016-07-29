
<?php 
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);
//Consulta de productos activos, en espera o finalizados
$params = array('access_token' => $_SESSION['access_token'],
                'attributes' =>'available_filters');
$result = $meli->get('/users/'.$_SESSION['userid'].'/items/search', $params);
$activas=$result['body']->available_filters[0]->values[3]->results;
$pausadas=$result['body']->available_filters[0]->values[4]->results;
$finalizadas=$result['body']->available_filters[0]->values[5]->results;
//realizamos la peticion a mercadolibre de las preguntas del vendedor
$params = array('access_token' => $_SESSION['access_token'],
                 'attributes' => 'total',
                 'status'=> 'UNANSWERED');
$result3 = $meli->get('/my/received_questions/search', $params);
$number=$result3['body']->total;

$result2= array ('activas'=> $activas,
                'pausadas'=> $pausadas,
                'finalizadas'=> $finalizadas,
                'sinresponder'=> $number);
if(($result['httpCode']==200) and ($result3['httpCode']==200)){
$result4['data']=$result2;
$result4['mensaje']='success';
}else
{
    $result4['mensaje']='Error al conectar con mercadolibre, pulse F5 para intentarlo nuevamente';
}
echo json_encode($result4);

 ?>
       
                    
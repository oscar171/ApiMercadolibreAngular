<?php 
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);


/*include_once '../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
//include_once '../DBconexion/Conf.class.php';
//$bd=Db::getInstance();
//$result3 = $meli->get('/orders/1144358391', $params);
//1144817491
$params = array('access_token' => $_SESSION['access_token'],
                'seller'=> $_SESSION['userid'],
                'offset'=> 200);
$result3 = $meli->get('/orders/1151580029', $params);

/*//$result1= $result3['body']->results[0]->buyer;
if($result3['httpCode']==200){
        if($result3['body']->feedback->purchase->fulfilled==1 && $result3['body']->feedback->sale->fulfilled==1 ){
            $status="Concretada";
        }else
        {
             $status="Aun no se ha concredado";
        }
$orderData=array('id' => $result3['body']->id,
             'date_created' => $result3['body']->date_created,
             'last_updated' => $result3['body']->last_updated,
             'shipping' => $result3['body']->shipping->status,
             'feedback'=> $result3['body']->feedback,
             'expiration_date'=> $result3['body']->expiration_date,
             'buyer'=> $result3['body']->buyer,
             'order_items' => array_shift($result3['body']->order_items),
             'status' => $status);
    $arrayResult['order'] = $orderData;
    $arrayResult['mensaje'] = "success";
}
else
{
        $arrayResult['mensaje']= "Error al conectarse a mercadolibre";
}
*/
/*echo "<pre>";
print_r($result3);
echo "</pre>";
*/
echo "<pre>";
print_r ($result3);
echo "</pre>";
 ?>
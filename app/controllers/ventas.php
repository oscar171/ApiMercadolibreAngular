<?php 
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);

$params = array('access_token' => $_SESSION['access_token']);
include_once '../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
include_once '../DBconexion/Conf.class.php';
$bd=Db::getInstance();
//$result3 = $meli->get('/orders/1144358391', $params);
//1144817491
$result3 = $meli->get('/orders/1148603035', $params);
  $sql="SELECT id FROM ventas WHERE id='".$result3['body']->id."'";
$bol=$bd->ejecutar($sql);
print_r($bol);
 if($bol)
 {
       /*$sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";*/
 $bd->ejecutar($sql);
   $sql="INSERT INTO ventas (id_orden, id_buyer, name_buyer,lastname_buyer,nickname_buyer,phone_buyer,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title)
 VALUES ('".$result3['body']->id."', '".$result3['body']->buyer->id."', '".$result3['body']->buyer->first_name."','".$result3['body']->buyer->last_name."','".$result3['body']->buyer->nickname."','".$result3['body']->buyer->phone->number."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."')";
 $respon=$bd->ejecutar($sql);
 $array['mensaje']="new_order";
 $array['data']=array("telefono"=>$result3['body']->buyer->phone->number,
     "new_order_id"=>$result3['body']->id
   );
 

 }
 else
 {
     if($result3['body']->feedback->purchase!=NULL)
     {
      
      $sql="UPDATE ventas
     SET rating='".$result3['body']->feedback->purchase->rating."' WHERE id='".$result3['body']->id."'";
     $respon=$bd->ejecutar($sql);
     if($respon){
     $array['mensaje']="calificacion comprador orden id: ".$result3['body']->id.$respon;
       }
       else{
         $array['mensaje']="no nada";
       }
     }
     else
     {
           $array['mensaje']="no han calificado";

     }


 }


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
print_r ($array);
echo "</pre>";
 ?>

<?php 
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
/*Incluimos el fichero de la clase Db*/
include_once '../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
include_once '../DBconexion/Conf.class.php';

$meli = new Meli(APP_ID, APP_KEY);
/*Creamos la instancia del objeto. Ya estamos conectados*/
$bd=Db::getInstance();
 $sql="SELECT recurso,userid,topic FROM notificaciones LIMIT 1";
 $result=$bd->ejecutar($sql);

if($result)
{
  $array='';
   while ($x=$bd->obtener_fila($result,0)) 
   {
       try{
        switch ($x['topic']) 
        {
                case 'questions':
                $ver= explode('/',$x['recurso']); 
                $params = array('access_token' => $_SESSION['access_token']);
                        $result3 = $meli->get('/questions/'.$ver[2], $params);
                        if($result3['httpCode']==200 || $result3['httpCode']==404 )
                        {
                                 $sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";
                                  $bd->ejecutar($sql);
                                if($result3['body']->status=="UNANSWERED")
                                {
                                        $sql="INSERT INTO preguntas (id, seller_id, item_id,status,fechaCreada,pregunta)
                                VALUES ('".$result3['body']->id."', '".$result3['body']->seller_id."', '".$result3['body']->item_id."','".$result3['body']->status."','".$result3['body']->date_created."','".$result3['body']->text."')";
                                $respon=$bd->ejecutar($sql);
                                 $array['mensaje']="Tienes una nueva pregunta: ".$respon;
                                }
                                if($result3['body']->status=="ANSWERED")
                                {
                                $sql="UPDATE preguntas
                                SET status='ANSWERED',fechaRespuesta='".$result3['body']->answer->date_created."', respuesta='".$result3['body']->answer->text."' WHERE id='".$result3['body']->id."'";
                                $respon=$bd->ejecutar($sql);
                                 $array['mensaje']="Respondistes una pregunta: ".$respon;
                                }
                                if($result3['body']->status==404)
                                {
                                  $sql="UPDATE preguntas
                                SET status='eliminada' WHERE id='".$ver[2]."'";
                                $respon=$bd->ejecutar($sql);
                                 $array['mensaje']="Eliminastes una pregunta: ".$respon;
                                }
                        }else
                        {
                          $array['mensaje']="error al conectar mercadolibre";
                        }

                        break;
                case 'created_orders':
                       $ver= explode('/',$x['recurso']); 
                                $params = array('access_token' => $_SESSION['access_token']);
                        $result3 = $meli->get('/orders/'.$ver[2], $params);
                        if($result3['httpCode']==200)
                                {
                                   $sql="SELECT id FROM ventas WHERE id='".$ver[2]."'";
                                   $bol=$bd->ejecutar($sql);
                                  if(!$bol)
                                  {
                                        $sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";
                                  $bd->ejecutar($sql);
                                    $sql="INSERT INTO ventas (id_orden, id_buyer, name_buyer,lastname_buyer,nickname_buyer,phone_buyer,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title)
                                  VALUES ('".$result3['body']->id."', '".$result3['body']->buyer->id."', '".$result3['body']->buyer->first_name."','".$result3['body']->buyer->last_name."','".$result3['body']->buyer->nickname."','".$result3['body']->buyer->phone->number."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."')";
                                  $respon=$bd->ejecutar($sql);
                                  $array['mensaje']="new_order";
                                  $array['data']=array("telefono"=>$result3['body']->buyer->phone->number,
                                      "new_order_id"=>$ver[2]
                                    );
                                  

                                  }
                                  else
                                  {
                                      if($result3['body']->feedback->purchase!=NULL)
                                      {
                                       
                                       $sql="UPDATE ventas
                                      SET rating='".$result3['body']->feedback->purchase->rating."' WHERE id='".$ver[2]."'";
                                      $respon=$bd->ejecutar($sql);
                                      if($respon){
                                      $array['mensaje']="Nueva calificacion del comprador: ".$result3['body']->buyer->first_name.$result3['body']->buyer->last_name.$respon;
                                        }
                                        else{
                                          print_r($respon);
                                        }
                                      }
                                      else
                                      {
                                            $array['mensaje']="no han calificado";

                                      }


                                  }
                              }
                              else{
                                $array['mensaje']="error al conectar mercadolibre";
                              }

                      break;
                    case 'orders':
                        $ver= explode('/',$x['recurso']); 
                                $params = array('access_token' => $_SESSION['access_token']);
                        $result3 = $meli->get('/orders/'.$ver[2], $params);
                        if($result3['httpCode']==200)
                                {
                                  
                                   $sql="SELECT id FROM ventas WHERE id_orden='".$ver[2]."'";
                                   $bol=$bd->ejecutar($sql);
                                  if(!$bol){
                                        /*$sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";
                                  $bd->ejecutar($sql);*/
                                  $sql="INSERT INTO ventas (id_orden, id_buyer, name_buyer,lastname_buyer,nickname_buyer,phone_buyer,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title)
                                VALUES ('".$result3['body']->id."', '".$result3['body']->buyer->id."', '".$result3['body']->buyer->first_name."','".$result3['body']->buyer->last_name."','".$result3['body']->buyer->nickname."','".$result3['body']->buyer->phone->number."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."')";
                                $respon=$bd->ejecutar($sql);
                                 $array['mensaje']="new_order";
                                 $array['data']=array("telefono"=>$result3['body']->buyer->phone->number,
                                    "new_order_id"=>$ver[2]
                                  );

                                    }
                                    else
                                    {
                                      if($result3['body']->feedback->purchase!=NULL){
                                       $sql="UPDATE ventas
                                      SET rating='".$result3['body']->feedback->purchase->rating."' WHERE id='".$result3['body']->id."'";
                                      $respon=$bd->ejecutar($sql);
                                      if($respon){
                                      $array['mensaje']="calificacion comprador orden id: ".$ver[2].$respon;
                                        }else
                                        {
                                          print_r($respon."no hay nada");
                                        }
                                      }
                                      else
                                      {
                                        $array['mensaje']="no han calificado";
                                      }

                                    }
                              }
                              else
                              {
                                $array['mensaje']="error al conectar mercadolibre";
                              }
                        break;
                default:
                        # code...
                        break;
          }              
        }catch (Exception $e) {
                echo 'Excepción capturada: ',  $e->getMessage(), "";
                        }

      
      
    }
 }else
 {
        
        $array['mensaje']='noData';

 }
//$questions=$result3['body']->questions;
echo "<pre>";
print_r($array);
echo "</pre>";
 ?>
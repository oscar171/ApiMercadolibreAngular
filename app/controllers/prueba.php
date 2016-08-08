
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
 $sql="SELECT recurso,userid,topic FROM notificaciones WHERE userid=".$_SESSION['userid']." LIMIT 1";
 $result=$bd->ejecutar($sql);
if(mysql_num_rows($result)>0)
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
                $sql="INSERT INTO preguntas (id, seller_id, item_id,status,fechaCreada,pregunta) VALUES ('".$result3['body']->id."', '".$result3['body']->seller_id."', '".$result3['body']->item_id."','".$result3['body']->status."','".$result3['body']->date_created."','".$result3['body']->text."')";
                $respon=$bd->ejecutar($sql);
                $config='title,thumbnail';
                $params2 = array('access_token' => $_SESSION['access_token'],'attributes' => $config);
                $result2 = $meli->get('/items/'.$result3['body']->item_id, $params2);
                $array['id']="new_question";
                $array['mensaje']="Te preguntaron algo";
                $array['title']=$result2['body']->title;
                $array['thumbnail']=$result2['body']->thumbnail;
                }
                if($result3['body']->status=="ANSWERED")
                {
                  $sql="SELECT id FROM preguntas WHERE id='".$ver[2]."'";
                  $bol=$bd->ejecutar($sql);
                  if(mysql_num_rows($bol)>0)
                  {
                  $sql="UPDATE preguntas SET status='ANSWERED',fechaRespuesta='".$result3['body']->answer->date_created."', respuesta='".$result3['body']->answer->text."' WHERE id='".$result3['body']->id."'";
                  $respon=$bd->ejecutar($sql);
                  $array['mensaje']="Respondieron una pregunta: ".$respon;
                  }
                  else
                  {
                    $sql="INSERT INTO preguntas (id, seller_id, item_id,status,fechaCreada,pregunta,fechaRespuesta,respuesta) VALUES ('".$result3['body']->id."', '".$result3['body']->seller_id."', '".$result3['body']->item_id."','".$result3['body']->status."','".$result3['body']->date_created."','".$result3['body']->text."','".$result3['body']->answer->date_created."','".$result3['body']->answer->text."')";
                      $respon=$bd->ejecutar($sql);
                  }
                  $array['mensaje']="Respondieron una pregunta: ".$respon;
                }
                if($result3['body']->status==404)
                {
                  $sql="UPDATE preguntas
                SET status='eliminada' WHERE id='".$ver[2]."'";
                $respon=$bd->ejecutar($sql);
                 $array['mensaje']="Pregunta eliminada: ".$respon;
                }
              }
              else
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
                   $sql="SELECT id_orden FROM ventas WHERE id_orden='".$ver[2]."'";
                   $bol=$bd->ejecutar($sql);
                      if(mysql_num_rows($bol)<=0)
                      {
                        $config='title,thumbnail';
                        $params2 = array('attributes' => $config);
                        $result2 = $meli->get('/items/'.$result3['body']->order_items[0]->item->id, $params2);
                      $sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";
                      $bd->ejecutar($sql);
                            if($result3['body']->payments==NULL)
                            {
                            $sql="INSERT INTO ventas (id_orden,id_seller,id_buyer,name_buyer,lastname_buyer,nickname_buyer,phone_buyer,phone_buyer2,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title,thumbnail,payment_type,monto) VALUES ('".$result3['body']->id."','".$result3['body']->seller->id."','".$result3['body']->buyer->id."','".$result3['body']->buyer->first_name."','".$result3['body']->buyer->last_name."','".$result3['body']->buyer->nickname."','".$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number."','".$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."','".$result2['body']->thumbnail."','AcordarVendedor','".$result3['body']->total_amount."')";
                            $respon=$bd->ejecutar($sql);
                            $array['id']="new_order";
                            $array['mensaje']="Te compraron algo";
                            $array['title']=$result2['body']->title;
                            $array['thumbnail']=$result2['body']->thumbnail;

                                if($result3['body']->buyer->alternative_phone->number!=NULL)
                                {
                                $array['data']=array("telefono1"=>$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number,
                                                      "telefono2"=>$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number,
                                                   "new_order_id"=>$ver[2]);
                                }
                                else
                                {
                                 $array['data']=array("telefono1"=>$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number,
                                                      "telefono2"=> NULL,
                                                   "new_order_id"=>$ver[2]); 
                                }
                            }
                            else
                            {
                              $sql="INSERT INTO ventas (id_orden,id_seller,id_buyer,nickname_buyer,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title,payment_method,payment_type,thumbnail,payment_status,monto)VALUES ('".$result3['body']->id."', '".$result3['body']->seller->id."', '".$result3['body']->buyer->id."','".$result3['body']->buyer->nickname."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."','".$result3['body']->payments[0]->payment_method_id."','MercadoPago','".$result2['body']->thumbnail."','".$result3['body']->payments[0]->status."','".$result3['body']->total_amount."')";
                                $respon=$bd->ejecutar($sql);
                                $array['id']="new_order";
                                $array['mensaje']="Te compraron algo MercadoPago";
                                $array['title']=$result2['body']->title;
                                $array['thumbnail']=$result2['body']->thumbnail;

                            }
                            if($result3['body']->feedback->purchase!=NULL)
                            {
                                     
                              $sql="UPDATE ventas SET status='".$result3['body']->status."',rating='".$result3['body']->feedback->purchase->rating."' WHERE id='".$ver[2]."'";
                              $respon=$bd->ejecutar($sql);
                              if($respon)
                              {
                                $array['mensaje']="calificacion comprador orden id: ".$ver[2].$respon;
                              }
                              else
                              {
                              print_r($respon);
                              }
                            }
                      }
                      else
                      {
                        if($result3['body']->feedback->purchase!=NULL)
                          {
                          $sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";
                           $bd->ejecutar($sql);              
                           $sql="UPDATE ventas SET rating='".$result3['body']->feedback->purchase->rating."' WHERE id_orden='".$ver[2]."'";
                            $respon=$bd->ejecutar($sql);
                              if($respon)
                              {
                                $array['mensaje']="calificacion comprador orden id: ".$ver[2].$respon;
                              }
                              else
                              {
                                 print_r($respon);
                              }
                            }
                            else
                            {
                            $sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";
                            $bd->ejecutar($sql);
                            $array['mensaje']="no han calificado";
                            }
                      }
                  }
                    else
                  {

                    $array['mensaje']="Error al conectar con mercadolibre";
                  }

          break;
          case'payments':
                    $params = array('access_token' => $_SESSION['access_token']);
                    $result3 = $meli->get($x['recurso'], $params);
                    $sql="UPDATE ventas SET status='".$result3['body']->status."', mount='".$result3['body']->total_paid_amount."' WHERE id_orden='".$result3['body']->order_id."'";
                            $respon=$bd->ejecutar($sql);
                    /*$sql="DELETE FROM notificaciones WHERE recurso = '".$x['recurso']."'";
                      $bd->ejecutar($sql);*/
                    $sql="SELECT thumbnail FROM ventas WHERE id_orden='".$result3['body']->order_id."'";
                    $respon=$bd->ejecutar($sql);
                    $x=$bd->obtener_fila($respon,0);
                      $array['id']="new_order";
                      $array['mensaje']="Te pagaron algo";
                      $array['title']=$result3['body']->reason;
                      $array['thumbnail']=$x['thumbnail'];
          break;
                      
          default:
          
          break;
          }              
        }catch (Exception $e)
        {
        echo 'Excepción capturada: ',  $e->getMessage(), "";
        }

      
      
    }
 }else
 {

 $array['mensaje']='noData';
}
//$questions=$result3['body']->questions;
echo json_encode($array);
 ?>
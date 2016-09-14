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
if(!isset($bd))
{
$bd=Db::getInstance();
}
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
                    $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
                    $bd->ejecutar($sql);
                    if($result3['body']->status=="UNANSWERED")
                    {
                    $config='title,thumbnail';
                    $params2 = array('access_token' => $_SESSION['access_token'],'attributes' => $config);
                    $result2 = $meli->get('/items/'.$result3['body']->item_id, $params2);
                    $array=insertarPregunta($result3,$bd,$result2);
                    }
                    if($result3['body']->status=="ANSWERED")
                    {
                      $sql="SELECT id FROM preguntas_ml WHERE id='".$ver[2]."'";
                      $bol=$bd->ejecutar($sql);
                      if(mysql_num_rows($bol)>0)
                      {
                      $sql="UPDATE preguntas_ml SET status='ANSWERED',fechaRespuesta='".$result3['body']->answer->date_created."', respuesta='".$result3['body']->answer->text."' WHERE id='".$result3['body']->id."'";
                      $respon=$bd->ejecutar($sql);
                      $array['mensaje']="Respondieron una pregunta: ".$respon;
                      }
                      else
                      {
                        $sql="INSERT INTO preguntas_ml (id, seller_id, item_id,status,fechaCreada,pregunta,fechaRespuesta,respuesta) VALUES ('".$result3['body']->id."', '".$result3['body']->seller_id."', '".$result3['body']->item_id."','".$result3['body']->status."','".$result3['body']->date_created."','".$result3['body']->text."','".$result3['body']->answer->date_created."','".$result3['body']->answer->text."')";
                          $respon=$bd->ejecutar($sql);
                      }
                      $array['mensaje']="Respondieron una pregunta: ".$respon;
                    }
                    if($result3['body']->status==404)
                    {
                      $sql="UPDATE preguntas_ml SET status='eliminada' WHERE id='".$ver[2]."'";
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
                   $sql="SELECT id_orden FROM ventas_ml WHERE id_orden='".$ver[2]."'";
                   $bol=$bd->ejecutar($sql);
                      if(mysql_num_rows($bol)<=0)
                      {
                        $config='title,thumbnail';
                        $params2 = array('attributes' => $config);
                        $result2 = $meli->get('/items/'.$result3['body']->order_items[0]->item->id, $params2);
                            if($result3['body']->payments==NULL)
                            {$array=insertar($result3,$bd,$result2,$x);}
                            else
                            {
                              if($result3['body']->status=='payment_required')
                              {$array=insertarMercadoPago($result3,$bd,$result2,$x);}
                              else
                              {$array=insertar($result3,$bd,$result2,$x);}

                            }
                            if($result3['body']->feedback->purchase->rating!=NULL)
                            {
                                     
                              $sql="UPDATE ventas_ml SET status='".$result3['body']->status."',rating='".$result3['body']->feedback->purchase->rating."' WHERE id='".$result3['body']->id."'";
                              $respon=$bd->ejecutar($sql);
                              if($respon)
                              {
                                $array['mensaje']="calificacion comprador orden id: ".$result3['body']->id.$respon;
                              }
                              else
                              {
                              print_r($respon);
                              }
                            }
                      }
                      else
                      {
                        if($result3['body']->feedback->purchase->rating=NULL)
                          {            
                           $sql="UPDATE ventas_ml SET rating='".$result3['body']->feedback->purchase->rating."' WHERE id_orden='".$result3['body']->id."'";
                            $respon=$bd->ejecutar($sql);
                              if($respon)
                              {
                                $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
                                $bd->ejecutar($sql);
                                $array['mensaje']="calificacion comprador orden id: ".$result3['body']->id.$respon;
                              }
                              else
                              {
                                 $array['mesaje']='no califico correctamente';
                              }
                            }
                            else
                            {
                            $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
                            $bd->ejecutar($sql);
                            $array['mensaje']="no han calificado";
                            }
                      }
                }
                else
                { $array['mensaje']="Error al conectar con mercadolibre";}

          break;
          case 'payments':

                    
                    $params = array('access_token' => $_SESSION['access_token']);
                    $result = $meli->get($x['recurso'], $params);
                    $params = array('access_token' => $_SESSION['access_token']);
                    $result3 = $meli->get('/orders/'.$result['body']->order_id, $params);
                    $sql="SELECT id_orden FROM ventas_ml WHERE id_orden='".$result['body']->order_id."'";
                    $bol=$bd->ejecutar($sql);
                    if(mysql_num_rows($bol>0))
                    {
                    $sql="UPDATE ventas_ml SET status='".$result3['body']->status."', mount='".$result['body']->total_paid_amount."' WHERE id_orden='".$result['body']->order_id."'";
                    $respon=$bd->ejecutar($sql);
                    }
                    else
                    {
                      $params2 = array('access_token' => $_SESSION['access_token'],'attributes' => 'thumbnail');
                     $result2 = $meli->get('/items/'.$result3['body']->order_items[0]->item->id, $params2);
                      $sql="INSERT INTO ventas_ml (id_orden,id_seller,id_buyer,name_buyer,lastname_buyer,nickname_buyer,phone_buyer,phone_buyer2,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title,thumbnail,payment_type,monto) VALUES ('".$result3['body']->id."','".$result3['body']->seller->id."','".$result3['body']->buyer->id."','".$result3['body']->buyer->first_name."','".$result3['body']->buyer->last_name."','".$result3['body']->buyer->nickname."','".$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number."','".$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."','".$result2['body']->thumbnail."','MercadoPago','".$result3['body']->total_amount."')";
                      $respon=$bd->ejecutar($sql);
                    }
                    $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
                      $bd->ejecutar($sql);
                      $array['id']="new_order";
                      $array['mensaje']="Te pagaron algo";
                      $array['title']=$result['body']->reason;
                      $array['thumbnail']=$result2['body']->thumbnail;
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

function insertar($result3,$bd,$result2,$x)
{

  $sql="INSERT INTO ventas_ml (id_orden,id_seller,id_buyer,name_buyer,lastname_buyer,nickname_buyer,phone_buyer,phone_buyer2,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title,thumbnail,payment_type,monto) VALUES ('".$result3['body']->id."','".$result3['body']->seller->id."','".$result3['body']->buyer->id."','".$result3['body']->buyer->first_name."','".$result3['body']->buyer->last_name."','".$result3['body']->buyer->nickname."','".$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number."','".$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."','".$result2['body']->thumbnail."','AcordarVendedor','".$result3['body']->total_amount."')";
    $respon=$bd->ejecutar($sql);
    if($respon)
    {
      $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
      $bd->ejecutar($sql);
      $array['id']="new_order";
      $array['mensaje']="Te compraron algo";
      $array['title']=$result2['body']->title;
      $array['thumbnail']=$result2['body']->thumbnail;

     if($result3['body']->buyer->alternative_phone->number!=NULL)
      {
        $phone1=$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number;
        $phone2=$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number;
        $phone1=limpiarString($phone1);
        $phone2=limpiarString($phone2);
      if(!preg_match("/^[0-9]{11}$/", $phone1)) //check for a pattern of 04140000000 
        { 
        $phone1="0".$phone1;
        }
        if(!preg_match("/^[0-9]{11}$/", $phone2)) //check for a pattern of 04140000000 
        { 
        $phone2="0".$phone2;
        }
        $sub=substr($phone1,0,-9);
      if($sub=='02')
        { $num=$_SESSION['phone'];}
        else
        {$num=$phone1;}
        $sub=substr($phone2,0,-9);
      if($sub=='02')
        { $num2=$_SESSION['phone'];}
        else
        {$num2=$phone1;}
        $array['data']=array
        (
        "telefono1"=>$num,
        "telefono2"=>$num2,
        "telefonoseller"=>$_SESSION['phone'],
        "new_order_id"=>$result3['body']->id
        );
      }
      else
      {
        $phone1=$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number;
        $phone1=limpiarString($phone1);
      if(!preg_match("/^[0-9]{11}$/", $phone1)) //check for a pattern of 91-0123456789 
        { 
        $phone1="0".$phone1;
        }
        $sub=substr($phone1,0,-9);
      if($sub=='02')
        { $num=$_SESSION['phone'];}
        else
        {$num=$phone1;}
      $array['data']=array
        (
        "telefono1"=>$num,
        "telefono2"=> NULL,
        "telefonoseller"=>$_SESSION['phone'],
        "new_order_id"=>$result3['body']->id
        ); 
      }
    }
  return $array;
                          
}
function insertarMercadoPago($result3,$bd,$result2,$x)
{
$sql="INSERT INTO ventas_ml (id_orden,id_seller,id_buyer,nickname_buyer,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title,payment_method,payment_type,thumbnail,payment_status,monto)VALUES ('".$result3['body']->id."', '".$result3['body']->seller->id."', '".$result3['body']->buyer->id."','".$result3['body']->buyer->nickname."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."','".$result3['body']->payments[0]->payment_method_id."','MercadoPago','".$result2['body']->thumbnail."','".$result3['body']->payments[0]->status."','".$result3['body']->total_amount."')";
  $respon=$bd->ejecutar($sql);
  if($respon)
  {
  $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
  $bd->ejecutar($sql);
  $array['id']="new_order";
  $array['mensaje']="Te compraron algo por MercadoPago";
  $array['telefonoseller']=$_SESSION['phone'];
  $array['title']=$result2['body']->title;
  $array['thumbnail']=$result2['body']->thumbnail;
  }

  return $array;

}

function insertarPregunta($result3,$bd,$result2)
{
$sql="INSERT INTO preguntas_ml (id, seller_id, item_id,status,fechaCreada,pregunta) VALUES ('".$result3['body']->id."', '".$result3['body']->seller_id."', '".$result3['body']->item_id."','".$result3['body']->status."','".$result3['body']->date_created."','".$result3['body']->text."')";
$respon=$bd->ejecutar($sql);

$array['id']="new_question";
$array['mensaje']="Te preguntaron algo";
$array['title']=$result2['body']->title;
$array['thumbnail']=$result2['body']->thumbnail;
return $array;
}
function limpiarString($texto)
{
      $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $texto);                
      return $textoLimpio;
}
 ?>

<?php 

/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* @package: Procesamiento de la notificacion enviada por mercadolibre, previamente almacenada en la 
*base de datos
*/
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
 $sql="SELECT recurso,userid,topic FROM notificaciones_ml WHERE userid=".$_SESSION['userid']." ORDER BY fecha_creacion ASC LIMIT 1 ";
 $result=$bd->ejecutar($sql);
if(mysqli_num_rows($result)>0)
{
  $array='';
   while ($x=$bd->obtener_fila($result,0)) 
   {
    try{
      //verficamos que topico es ejemplo (questions, created_orders o payments)(preguntas, ordenes o pagos)
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
                    //$config contiene los atributos los cuales necesito y por lo tanto mercadolibre filtra los demas atributos y me envia solo la informacion de los atributos que yo le pase en la variable config 
                    $config='title,thumbnail,seller_custom_field';
                    $params2 = array('access_token' => $_SESSION['access_token'],'attributes' => $config);
                    $result2 = $meli->get('/items/'.$result3['body']->item_id, $params2);
                    $array=insertarPregunta($result3,$bd,$result2);
                    }
                    if($result3['body']->status=="ANSWERED")
                    {
                      $sql="SELECT id FROM preguntas_ml WHERE id='".$ver[2]."'";
                      $bol=$bd->ejecutar($sql);
                      if(mysqli_num_rows($bol)>0)
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
                      if(mysqli_num_rows($bol)<=0)
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
                              {$array=insertarMercadoPagoCompleto($result3,$bd,$result2,$x);}

                            }
                            if($result3['body']->feedback->purchase!=NULL)
                            {
                                     
                              $sql="UPDATE ventas_ml SET status='".$result3['body']->status."',rating='".$result3['body']->feedback->purchase->rating."' WHERE id='".$result3['body']->id."'";
                              $respon=$bd->ejecutar($sql);
                              if($respon)
                              {
                                $array['mensaje']="calificacion comprador orden id: ".$result3['body']->id;
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
                           $sql="UPDATE ventas_ml SET rating='".$result3['body']->feedback->purchase->rating."' WHERE id_orden='".$result3['body']->id."'";
                            $respon=$bd->ejecutar($sql);
                              if($respon)
                              {
                                $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
                                $bd->ejecutar($sql);
                                $array['mensaje']="calificacion comprador orden id: ".$result3['body']->id;
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
                    if(mysqli_num_rows($bol)>0)
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
echo json_encode($array);

/**
* Funcion que se utiliza para insertar (guardar) la informacion de una comprar (orden), ofertada o 
* comprada por el metodo de pago (ACORDAR CON EL VENDEDOR). 
*
*
*@param json $result3: un objeto de tipo json enviado desde mercadolibre, 
*con todos los datos de la orden consultada
*@param SQL $bd: objeto de tipo SQL que contiene toda la conexion y a la configuracion a la base de
*datos
*@param json $result2: objeto de tipo json enviado desde mercadolibre con toda la informacion del *producto relacionado con la orden
*@param array $x: objeto tipo array que contiene el id del recurso de la notificacion con la que se 
*esta trabajando
*@var $phone1 string: variable que se usa para almacenar el primer telefono del comprador
*@var $phone2 string: variable que se usa para almacenar el segundo telefono del comprador
*@var $sub string: variable que se usa para almacenar los primeros 2 caracteres de los telefonos, para 
*verificar el codigo de area y ver si es un telefono local o un celular
*@return array :retorna un objeto tipo array que contiene la informacion requerida para mostrarla en *la vista.
*/
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
      $fecha= new DateTime($result3['body']->date_created);
      $fechas=$fecha->format('d-m-Y');
      $hora= $fecha->format('H:i:s');

       if($result3['body']->buyer->alternative_phone->number!=NULL)
        {
          $phone1=$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number;
          $phone2=$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number;
          $phone1=limpiarString($phone1);
          $phone2=limpiarString($phone2);
          if(!preg_match("/^[0-9]{11}$/", $phone1)) //check for a pattern of 04140000000 
            {$phone1="0".$phone1;}
            if(!preg_match("/^[0-9]{11}$/", $phone2)) //check for a pattern of 04140000000 
            {$phone2="0".$phone2;}

            $sub=substr($phone1,0,-9);
          if($sub=='02'){$phone1=$_SESSION['phone'];}
            
            $sub=substr($phone2,0,-9);
          if($sub=='02'){ $phone2=$_SESSION['phone'];}

          $array['data']=array
          (
          "telefono1"=>$phone1,
          "telefono2"=>$phone2,
          "new_order_id"=>$result3['body']->id
          );
           $datosOrden=array
            (
              "orden_id"=>$result3['body']->id,  
              "fecha"=>$fechas,
              "hora" =>$hora,
              "cantidad"=>$result3['body']->order_items[0]->quantity,
              "precio"=>$result3['body']->order_items[0]->unit_price,
              "comprador_nickname"=>$result3['body']->buyer->nickname, 
              "comprador_telf1"=>$phone1,
              "comprador_telf2"=>$phone2,
              "comprador_nombres"=>$result3['body']->buyer->first_name,
              "comprador_apellidos"=>$result3['body']->buyer->last_name, 
              "tipo_pago"=>0 ,
              "publicacion_id"=>$result3['body']->order_items[0]->item->id,
              "comprador_nickname"=> $result3['body']->buyer->nickname,
              "vendedor_id" => $result3['body']->seller->id,
              
              "total_compra"=>$result3['body']->total_amount
            );
          $array['mprsResp']=crearPedidoMprs($datosOrden);
          
        }
        else
        {
          $phone1=$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number;
          $phone1=limpiarString($phone1);
          if(!preg_match("/^[0-9]{11}$/", $phone1)) //check for a pattern of 04140000000 
            {$phone1="0".$phone1;}
            $sub=substr($phone1,0,-9);
          if($sub=='02')
            {$phone1=$_SESSION['phone'];}
          $array['data']=array
            (
            "telefono1"=>$phone1,
            "telefono2"=> NULL,
            "telefonoseller"=>$_SESSION['phone'],
            "new_order_id"=>$result3['body']->id
            );
             $datosOrden=array
              (
                "orden_id"=>$result3['body']->id,  
                "fecha"=>$fechas,
                "hora" =>$hora,
                "cantidad"=>$result3['body']->order_items[0]->quantity,
                "precio"=>$result3['body']->order_items[0]->unit_price,
                "comprador_nickname"=>$result3['body']->buyer->nickname, 
                "comprador_telf1"=>$phone1,
                "comprador_telf2"=>'',
                "comprador_nombres"=>$result3['body']->buyer->first_name,
                "comprador_apellidos"=>$result3['body']->buyer->last_name, 
                "tipo_pago"=>0 ,
                "publicacion_id"=>$result3['body']->order_items[0]->item->id,
                "comprador_nickname"=> $result3['body']->buyer->nickname,
                "vendedor_id" => $result3['body']->seller->id,
                
                "total_compra"=>$result3['body']->total_amount
              );
              $array['mprsResp']=crearPedidoMprs($datosOrden);

        }
    }
  return $array;
                          
}
/**
* Funcion que se utiliza para insertar (guardar) la informacion de una comprar (orden), ofertada o 
*comprada por el metodo de pago (MERCADOPAGO). 
*
*
*@param json $result3: un objeto de tipo json enviado desde mercadolibre, 
*con todos los datos de la orden consultada
*@param SQL $bd: objeto de tipo SQL que contiene toda la conexion y a la configuracion a la base de
*datos
*@param json $result2: objeto de tipo json enviado desde mercadolibre con toda la informacion del *producto relacionado con la orden
*@param array $x: objeto tipo array que contiene el id del recurso de la notificacion con la que se 
*
*esta trabajando
*@var $fecha date: variable que se usa para crear y almacenar un nuevo objeto tipo date
*@var $fechas date: variable que se usa para almacenar solo la fecha de el objeto date creado
*@var $hora date: variable que se usa para almacenar solo las horas de el objeto tipo date creado
*
*@return array :retorna un objeto tipo array que contiene la informacion requerida para mostrarla en *la vista.
*/
function insertarMercadoPago($result3,$bd,$result2,$x)
{
$sql="INSERT INTO ventas_ml (id_orden,id_seller,id_buyer,nickname_buyer,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title,payment_method,payment_type,thumbnail,payment_status,monto)VALUES ('".$result3['body']->id."', '".$result3['body']->seller->id."', '".$result3['body']->buyer->id."','".$result3['body']->buyer->nickname."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."','".$result3['body']->payments[0]->payment_method_id."','MercadoPago','".$result2['body']->thumbnail."','".$result3['body']->payments[0]->status."','".$result3['body']->total_amount."')";
  $respon=$bd->ejecutar($sql);
  if($respon)
  {
    $fecha= new DateTime($result3['body']->date_created);
    $fechas=$fecha->format('d-m-Y');
    $hora= $fecha->format('H:i:s');
    $array['id']="new_order";
    $array['mensaje']="Te compraron algo por MercadoPago";
    $array['telefonoseller']=$_SESSION['phone'];
    $array['title']=$result2['body']->title;
    $array['thumbnail']=$result2['body']->thumbnail;
    $array['data']=array("new_order_id"=>$result3['body']->id);
    $datosOrden=array
          (
            "orden_id"=>$result3['body']->id,  
            "fecha"=>$fechas,
            "hora" =>$hora,
            "cantidad"=>$result3['body']->order_items[0]->quantity,
            "precio"=>$result3['body']->order_items[0]->unit_price,
            "comprador_nickname"=>$result3['body']->buyer->nickname, 
            "comprador_telf1"=>'' ,
            "comprador_telf2"=>'' ,
            "comprador_nombres"=>'', 
            "comprador_apellidos"=>'', 
            "tipo_pago"=>1 ,
            "publicacion_id"=>$result3['body']->order_items[0]->item->id,
            "comprador_nickname"=> $result3['body']->buyer->nickname,
            "vendedor_id" => $result3['body']->seller->id,
            
            "total_compra"=>$result3['body']->total_amount
          );
    $array['mprsResp']=crearPedidoMprs($datosOrden);
  $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
  $bd->ejecutar($sql);
  }

  return $array;

}

/**
* Funcion que se utiliza para insertar (guardar) la informacion de una comprar (orden), ofertada o 
*comprada por el metodo de pago (MERCADOPAGO) y a su vez el pago fue hecho por tarjeta de credito, el 
*cual libera todos los datos del comprador de forma automatica  
*
*
*@param json $result3: un objeto de tipo json enviado desde mercadolibre, 
*con todos los datos de la orden consultada
*@param SQL $bd: objeto de tipo SQL que contiene toda la conexion y a la configuracion a la base de
*datos
*@param json $result2: objeto de tipo json enviado desde mercadolibre con toda la informacion del *producto relacionado con la orden
*@param array $x: objeto tipo array que contiene el id del recurso de la notificacion con la que se 
*esta trabajando
*@var $fecha date: variable que se usa para crear y almacenar un nuevo objeto tipo date
*@var $fechas date: variable que se usa para almacenar solo la fecha de el objeto date creado
*@var $hora date: variable que se usa para almacenar solo las horas de el objeto tipo date creado
*@return array :retorna un objeto tipo array que contiene la informacion requerida para mostrarla en *la vista con respecto a la orden.
*/
function insertarMercadoPagoCompleto($result3,$bd,$result2,$x)
{
$sql="INSERT INTO ventas_ml (id_orden,id_seller,id_buyer,nickname_buyer,fecha_creacion,fecha_expiracion,envio,status,item_id,item_title,payment_method,payment_type,thumbnail,payment_status,monto,name_buyer,lastname_buyer,phone_buyer,phone_buyer2)VALUES ('".$result3['body']->id."', '".$result3['body']->seller->id."', '".$result3['body']->buyer->id."','".$result3['body']->buyer->nickname."','".$result3['body']->date_created."','".$result3['body']->expiration_date."','".$result3['body']->shipping->status."','".$result3['body']->status."','".$result3['body']->order_items[0]->item->id."','".$result3['body']->order_items[0]->item->title."','".$result3['body']->payments[0]->payment_method_id."','MercadoPago','".$result2['body']->thumbnail."','".$result3['body']->payments[0]->status."','".$result3['body']->total_amount."','".$result3['body']->buyer->first_name."','".$result3['body']->buyer->last_name."','".$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number."','".$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number."')";
  $respon=$bd->ejecutar($sql);
  if($respon)
  {
    $fecha= new DateTime($result3['body']->date_created);
    $fechas=$fecha->format('d-m-Y');
    $hora= $fecha->format('H:i:s');
    $array['id']="new_order";
    $array['mensaje']="Te compraron y pagaron algo por MercadoPago";
    $array['title']=$result2['body']->title;
    $array['thumbnail']=$result2['body']->thumbnail;
    $array['data']=array("new_order_id"=>$result3['body']->id);

    if($result3['body']->buyer->alternative_phone->number!=NULL)
        {
          $phone1=$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number;
          $phone2=$result3['body']->buyer->alternative_phone->area_code.$result3['body']->buyer->alternative_phone->number;
          $phone1=limpiarString($phone1);
          $phone2=limpiarString($phone2);
          if(!preg_match("/^[0-9]{11}$/", $phone1)) //check for a pattern of 04140000000 
            {$phone1="0".$phone1;}
            if(!preg_match("/^[0-9]{11}$/", $phone2)) //check for a pattern of 04140000000 
            {$phone2="0".$phone2;}

            $sub=substr($phone1,0,-9);
          if($sub=='02'){$phone1=$_SESSION['phone'];}
            
            $sub=substr($phone2,0,-9);
          if($sub=='02'){ $phone2=$_SESSION['phone'];}

          $array['data']=array
          (
          "telefono1"=>$phone1,
          "telefono2"=>$phone2,
          "new_order_id"=>$result3['body']->id
          );
           $datosOrden=array
            (
              "orden_id"=>$result3['body']->id,  
              "fecha"=>$fechas,
              "hora" =>$hora,
              "cantidad"=>$result3['body']->order_items[0]->quantity,
              "precio"=>$result3['body']->order_items[0]->unit_price,
              "comprador_nickname"=>$result3['body']->buyer->nickname, 
              "comprador_telf1"=>$phone1,
              "comprador_telf2"=>$phone2,
              "comprador_nombres"=>$result3['body']->buyer->first_name,
              "comprador_apellidos"=>$result3['body']->buyer->last_name, 
              "tipo_pago"=>1,
              "publicacion_id"=>$result3['body']->order_items[0]->item->id,
              "comprador_nickname"=> $result3['body']->buyer->nickname,
              "vendedor_id" => $result3['body']->seller->id,
              
              "total_compra"=>$result3['body']->total_amount
            );
          $array['mprsResp']=crearPedidoMprs($datosOrden);
          
        }
        else
        {
          $phone1=$result3['body']->buyer->phone->area_code.$result3['body']->buyer->phone->number;
          $phone1=limpiarString($phone1);
          if(!preg_match("/^[0-9]{11}$/", $phone1)) //check for a pattern of 04140000000 
            {$phone1="0".$phone1;}
            $sub=substr($phone1,0,-9);
          if($sub=='02')
            {$phone1=$_SESSION['phone'];}
          $array['data']=array
            (
            "telefono1"=>$phone1,
            "telefono2"=> NULL,
            "telefonoseller"=>$_SESSION['phone'],
            "new_order_id"=>$result3['body']->id
            );
             $datosOrden=array
              (
                "orden_id"=>$result3['body']->id,  
                "fecha"=>$fechas,
                "hora" =>$hora,
                "cantidad"=>$result3['body']->order_items[0]->quantity,
                "precio"=>$result3['body']->order_items[0]->unit_price,
                "comprador_nickname"=>$result3['body']->buyer->nickname, 
                "comprador_telf1"=>$phone1,
                "comprador_telf2"=>'',
                "comprador_nombres"=>$result3['body']->buyer->first_name,
                "comprador_apellidos"=>$result3['body']->buyer->last_name, 
                "tipo_pago"=>1 ,
                "publicacion_id"=>$result3['body']->order_items[0]->item->id,
                "comprador_nickname"=> $result3['body']->buyer->nickname,
                "vendedor_id" => $result3['body']->seller->id,
                
                "total_compra"=>$result3['body']->total_amount
              );
              $array['mprsResp']=crearPedidoMprs($datosOrden);
                  
        }

    $sql="DELETE FROM notificaciones_ml WHERE recurso = '".$x['recurso']."'";
    $bd->ejecutar($sql);
  }

  return $array;

}


/**
* Funcion que se utiliza para insertar (guardar) la informacion de una pregunta realizada 
*
*
*@param json $result3: un objeto de tipo json enviado desde mercadolibre, 
*con todos los datos de la pregunta consultada
*@param SQL $bd: objeto de tipo SQL que contiene toda la conexion y a la configuracion a la base de
*datos
*@param json $result2: objeto de tipo json enviado desde mercadolibre con toda la informacion del *producto relacionado con la pregunta
*@param array $x: objeto tipo array que contiene el id del recurso de la notificacion con la que se 
*esta trabajando, para luego de ser procesada, eliminarla de la bd
*@var $array array 
*@return array :retorna un objeto tipo array que contiene la informacion requerida para mostrarla en *la vista con respecto a la pregunta.
*/

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
/**
* Funcion que se utiliza para limpiar de caracteres especiales los numeros de los compradores 
*
*@var $textoLimpio string : variable que se usa para almacenar el texto sin los caracteres especiales 
*@param string $texto: un objeto de tipo string que contiene un numero de telefono 
*@return string :retorna un string con el numero de telefono, eliminando todo los caracteres especiales
*en caso de poseer.
*/

function limpiarString($texto)
{
      $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $texto);                
      return $textoLimpio;
}

function crearPedidoMprs($datamprs,$bd)
{
 

  $datos['datamprs']=json_encode($datamprs);
  $datos['mprs_lu']='crcaicedo@gmail.com';
  $datos['mprs_lp']='OrionCorp34';
  $url='https://www.orioncorp.com.ve/mprs/ml_pedido_crear.php';
  $ch = curl_init($url);
  # Setup request to send json via POST.
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $datos );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  # Send request.
  $result = curl_exec($ch);

  // hacemos lo que queramos con los datos recibidos
  // por ejemplo, los mostramos
  error_log($result);
  $respond=json_decode($result);
  if ($respond->ok=='1')
  {
    $sql="UPDATE ventas_ml SET idmprs='".$respond->id."' WHERE id='".$datamprs['orden_id']."'";
    $respon=$bd->ejecutar($sql);
    if($respon)
    {
      $arrayRespuesta['mensaje']='success';
      $arrayRespuesta['id']=$respond->id;
    }
  }
  else
  {
    $arrayRespuesta['mensaje']='failed';
  }

  return $arrayRespuesta;
}
 
 ?>

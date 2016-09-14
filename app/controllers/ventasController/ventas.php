<?php 


/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* @package: obtenemos los datos de la ventas realizadas por un vendedor en especifico 
*
*/
session_start();
include_once '../../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
include_once '../../DBconexion/Conf.class.php';

    $bd=Db::getInstance();
    $sql="SELECT * FROM  `ventas_ml` WHERE id_seller = '".$_SESSION['userid']."' ORDER BY fecha_creacion DESC"; 
    $result=$bd->ejecutar($sql);
    $arrayResult['custom']=mysqli_num_rows($result);
    if(mysqli_num_rows($result)>0)
    {
        while ($x=$bd->obtener_fila($result,0))
        {

            ($x['rating']!=NULL)?$rating=$x['rating']:$rating="Aun sin calificar";
            ($x['envio']=='to_be_agreed')?$envio='Pendiente':$envio=$x['envio'];
            ($x['status']=='confirmed')?$status="Pendiente":$status=$x['status'];

           $orderData=array('Orden' => $x['id_orden'],
                 'Comprador' => $x['name_buyer']." ".$x['lastname_buyer'],
                 'Pseudonimo' => $x['nickname_buyer'],
                 'Telefonos' => $x['phone_buyer']." ".$x['phone_buyer2'],
                 'Item'=> $x['item_title'],
                 'Comprado'=> $x['fecha_creacion'],
                 'tipo' => $x['payment_type'],
                 'Pago'=> $status,
                 'Envio' => $envio,
                 'Calificacion' => $rating,
                 'thumbnail'=> $x['thumbnail']);
        $elemt[] = $orderData;
        
        }
        $arrayResult['order']=$elemt;
        $arrayResult['mensaje'] = "success";
    }
    else
    $arrayResult['mensaje']= "nodata";
    
 echo json_encode($arrayResult);
 ?>
 
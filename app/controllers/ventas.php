<?php 
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);


include_once '../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
include_once '../DBconexion/Conf.class.php';

/*$params = array('access_token' => $_SESSION['access_token'],
                'seller'=> $_SESSION['userid']);
$result3 = $meli->get('/orders/1150765955', $params);
*/

    $bd=Db::getInstance();
    $sql="SELECT * FROM  `ventas` WHERE id_seller = '".$_SESSION['userid']."' ORDER BY fecha_creacion DESC"; 
    $result=$bd->ejecutar($sql);
    //WHERE userid=".$_SESSION['userid']."";
    if(mysql_num_rows($result)>0)
    {
        while ($x=$bd->obtener_fila($result,0)) {

            if($x['rating']!=NULL)
            {
                $rating=$x['rating'];
            }
            else
            {
             $rating="Aun sin calificar";   
            }
            if($x['envio']=='to_be_agreed')
            {
            $envio='Pendiente';
            }
            else
            {
             $envio=$x['envio'];   
            }
            if($x['status']=='confirmed')
            {
                $status="Pendiente";
            }
            else
            {
                $status=$x['status'];

            }
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
    {
        $arrayResult['mensaje']= "nodata";
    }
 echo json_encode($arrayResult);
 ?>
 
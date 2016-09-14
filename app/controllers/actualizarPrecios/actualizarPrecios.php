<?php 
session_start();
require_once '../../libs_php/Mercadilivre/Meli/meli.php';
require_once '../../config.php';
include_once '../../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
include_once '../../DBconexion/Conf.class.php';

/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* 
*/

$bd=Db::getInstance();
//incicializamos el objeto de la clase mercadolibre con el id y la clave de la app
$meli = new Meli(APP_ID, APP_KEY);
$params = array('access_token' => $_SESSION['access_token'],
				 'status'=>'active');
//realizamos la peticion a mercadolibre de todos los productos que tiene en su cuenta
$result = $meli->get('/users/'.$_SESSION['userid'].'/items/search', $params);
//verificamos que la peticion se alla realizado con exito
if($result['httpCode']==200)
{//obtenemos la cantidad de productos que posee en su cuenta
$numitems=$result['body']->paging->total;
$itemsids='';
//obtenemos los id de los productos que posee en su cuenta
$items=$result['body']->results;
$update=0;
while(!empty($items))
{$itemsids=$itemsids.array_shift($items).',';}
	$config='id,title,price,seller_custom_field';
    $params2 = array('access_token' => $_SESSION['access_token'],'ids' => $itemsids,'attributes' => $config);
    $itemsResult=array();
    $result2 = $meli->get('/items', $params2);
    $arrayItem=$result2['body'];
    if($result2['httpCode']==200)
    {
	    while (!empty($arrayItem))
	    {
		     $item=array_shift($arrayItem);
		     $sql="SELECT new_price from notificacionesPrecio_ml where id_codigo='".$item->seller_custom_field."'";
		     $result=$bd->ejecutar($sql);
		     $x=$bd->obtener_fila($result,0);
		     if(mysqli_num_rows($result)>0 AND $item->price!=$x['new_price'] )
		     {
		       $params = array('access_token' => $_SESSION['access_token']);
			   $body= array('price'=>$x['new_price']);
			   $response= $meli->put('items/'.$item->id,$body,$params);
		      if($response['httpCode']==200)
		      	{$itemsResult['update']=$update=$update+1;
		      	$items[]=$item->title;
		      	$itemsResult['mensaje']='success';
		  		}else
		  		{
		  		$data['mensaje']="error al actualizar";
		  		$arrayItem='';
		  		}
		     }
		     else
		     	{ $itemsResult['mensaje']="no hay nuevos precios";}
	  	 
	     }
		
	}
else{$itemsResult= array('mensaje'=> 'error');}
}
else
{$itemsResult= array('mensaje'=> 'error');}
echo json_encode($itemsResult);
?>
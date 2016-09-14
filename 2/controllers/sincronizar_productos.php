<?php
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
//incicializamos el objeto de la clase mercadolibre con el id y la clave de la app
$meli = new Meli(APP_ID, APP_KEY);
$params = array('access_token' => $_SESSION['access_token']);
//realizamos la peticion a mercadolibre de todos los productos que tiene en su cuenta
$result = $meli->get('/users/'.$_SESSION['userid'].'/items/search', $params);
//verificamos que la peticion se alla realizado con exito
if($result['httpCode']==200)
{//obtenemos la cantidad de productos que posee en su cuenta
$numitems=$result['body']->paging->total;
$itemsids='';
//obtenemos los id de los productos que posee en su cuenta
$items=$result['body']->results;while(!empty($items))
{$itemsids=$itemsids.array_shift($items).',';}$config='id,title,price,status,thumbnail,listing_type_id';
    $params2 = array('ids' => $itemsids,'attributes' => $config);
    $itemsResult=array();
    $result2 = $meli->get('/items', $params2);
    $arrayItem=$result2['body'];if ($result2['httpCode']==200) {while (!empty($arrayItem)) {$item=array_shift($arrayItem);
if($item->status=='active'){$accion='Pausar';$accion2='Finalizar';}
if($item->status=='paused'){$accion='Activar';$accion2='Finalizar';}
if($item->status=='closed'){$accion='Republicar';$accion2='Eliminar';}
$items=array( 'id'=> $item->id,'thumbnail'=>$item->thumbnail,'listing_type_id'=>$item->listing_type_id,'title'=> $item->title,'price'=> $item->price,'status'=> $item->status,'accion'=> $accion,'accion2'=> $accion2);
$itemsResult[]=$items;
}$data['data']=$itemsResult;
$data['mensaje']='success';}
else{$itemsResult= array('mensaje'=> 'error');}}
else{$itemsResult= array('mensaje'=> 'error');}
echo json_encode($data);?>
<?php 
$data = json_decode(file_get_contents("php://input"));

include_once '../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
include_once '../DBconexion/Conf.class.php';


$bd=Db::getInstance();
$sql="INSERT INTO `notificacionesPrecio_ML` id_codigo,new_price VALUES ('".$data[0]->codigoid."','".$data[1]->price."'"; 
    $result=$bd->ejecutar($sql);
   if($result)
   	$array['mensaje']="success";
   else
   	$array['mensaje']="failed";


echo json_encode($array);
 ?>
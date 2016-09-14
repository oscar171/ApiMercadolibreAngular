<?php
/**
 * ML Notifications Listener
 **/
$servidor="localhost";
$usuario="venegang_api";
$password="1990102055a+";
$basededatos="venegang_uvprestashop";
$con=mysqli_connect($servidor,$usuario,$password, $basededatos );
if(mysqli_connect_errno()) {
$datos= "Error, no se pudo conectar";
exit;
}
else{

header("HTTP/1.1 200 OK");


$notif =file_get_contents("php://input");
$opcion=json_decode($notif);

$sql = sprintf("INSERT INTO `notificaciones_ml` (`recurso`, `userid`, `topic`) 
              VALUES ('%s', '%s', '%s')"
              , mysqli_real_escape_string( $con, $opcion->resource )
              , mysqli_real_escape_string( $con, $opcion->user_id )
              , mysqli_real_escape_string( $con, $opcion->topic )  );
$datos = mysqli_query($con, $sql);
if($datos)
{



}


$items2 = array('resource' => $opcion->resource,
				'userid'=>$opcion->user_id,
				'topic'=>$opcion->topic,
				'recibido'=>$opcion->received,
                 'resultado'=>$datos);
   
file_put_contents('notifica.txt',json_encode($items2).",", FILE_APPEND);
}
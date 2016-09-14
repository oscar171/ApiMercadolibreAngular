<?php 
session_start();
require_once '../../libs_php/Mercadilivre/Meli/meli.php';
require_once '../../config.php';
$meli = new Meli(APP_ID, APP_KEY);

$array=array(
            "orden_id"=>'123456',  
            "fecha"=>'12-09-2016',
            "hora" =>'14:12:16',
            "cantidad"=>'3',
            "precio"=>'1531',
            "comprador_nickname"=>'VENEGANGA4', 
            "comprador_telf1"=>'04149414743',
            "comprador_telf2"=>'',
            "comprador_nombres"=>'nombres',
            "comprador_apellidos"=>'apellidos', 
            "tipo_pago"=>1,
            "publicacion_id"=>'MLV465751101',
            "vendedor_id" => '195168514',
            "producto_codigo "=>'',
            "total_compra"=>'4593'
          );

$datos['datamprs']=json_encode($array);
$datos['mprs_lu']='crcaicedo@gmail.com';
$datos['mprs_lp']='OrionCorp34';
 //error_log(json_encode($array));
$url='https://www.orioncorp.com.ve/mprs/ml_pedido_crear.php';
$ch = curl_init( $url );
# Setup request to send json via POST.
curl_setopt( $ch, CURLOPT_POSTFIELDS, $datos );
//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
# Return response instead of printing.
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
# Send request.
$result = curl_exec($ch);

// hacemos lo que queramos con los datos recibidos
// por ejemplo, los mostramos
print_r($result);
$respond=json_decode($result);
echo "mensaje: ";
print_r($respond->msg);
echo "mensaje error: <br>";
print_r($respond->error);
echo "<br> mensaje ok: <br>";
print_r($respond->ok);
echo "<br> mensaje id: <br>";
print_r($respond->id);
?>
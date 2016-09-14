<?php 

/**
* @author : oscar perez <oscarp171@gmail.com>
* @version: 1.0
* @package: obtenemos los datos de todas las preguntas que le fueron realizada a todos los vendedores, 
*es decir todas las cuentas que han sido asociadas a la aplicacion.
*(SOLO VISIBLE PARA LOS USUARIOS ADMINISTRADOR) 
*
*/

session_start();
include_once '../../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
include_once '../../DBconexion/Conf.class.php';

    $bd=Db::getInstance();
    $sql="SELECT seller_id,fechaCreada,pregunta,respuesta FROM  `preguntas_ml` ORDER BY fechaCreada DESC LIMIT 3"; 
    $result=$bd->ejecutar($sql);
    if(mysqli_num_rows($result)>0)
    {
        while ($x=$bd->obtener_fila($result,0)) {
            $date= new DateTime($x['fechaCreada']);
           
           $preguntaData=array('vendedor' => $x['seller_id'],
                 'fechaCreada' => $date->format('d-m-Y'),
                 'pregunta' => $x['pregunta'],
                 'respuesta'=> $x['respuesta']);
        $elemt[] = $preguntaData;
        
        }
        $arrayResult['preguntas']=$elemt;
        $arrayResult['mensaje'] = "success";
    }
    else
    {
        $arrayResult['mensaje']= "nodata";
    }


echo json_encode($arrayResult);

 ?>
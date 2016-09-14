<?php 
session_start();
require_once '../../DBconexion/DB.class.php';
/*Incluimos el fichero de la clase Conf*/
require_once '../../DBconexion/Conf.class.php';

    $bd=Db::getInstance();
    $sql="SELECT * FROM  `preguntas` WHERE id_seller = '".$_SESSION['userid']."' ORDER BY fecha_creacion DESC"; 
    $result=$bd->ejecutar($sql);
    //WHERE userid=".$_SESSION['userid']."";
    if(mysql_num_rows($result)>0)
    {
        while ($x=$bd->obtener_fila($result,0)) {

           
           $preguntaData=array('vendedor' => $x['seller_id'],
                 'fechaCreada' => $x['fechaCreada'],
                 'pregunta' => $x['pregunta'],
                 'fechaRespuesta' => $x['fechaRespuesta'],
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
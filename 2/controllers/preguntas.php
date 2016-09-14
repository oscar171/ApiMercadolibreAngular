<?php 
session_start();
require_once '../libs_php/Mercadilivre/Meli/meli.php';
require_once '../config.php';
$meli = new Meli(APP_ID, APP_KEY);
$params = array('access_token' => $_SESSION['access_token'],
                'seller_id' => $_SESSION['userid'],
                'attributes' => 'questions,total',
                'status'=> 'UNANSWERED',
                'limit' => 5);
$result3 = $meli->get('/my/received_questions/search', $params);
if($result3['httpCode']==200)
{
$questions=$result3['body']->questions;
$arrayQuestion['total']=$result3['body']->total;
        if($result3['body']->total!=0)
        {
            while (!empty($questions)) 
            {
              $questions2=array_shift($questions);
              $idQuestion=$questions2->id;
              $item=$questions2->item_id;
              $text=$questions2->text;
              $params2 = array('attributes' => 'title');
              $result2 = $meli->get('/items/'.$item, $params2);
              $iteminfo=$result2['body'];
              $element= array(
              'title' => $iteminfo->title,
              'text' => $text,
              'idQuestion'=> $idQuestion);
                              
              $arrayElement[]=$element;                
            }
        $arrayQuestion['question']=$arrayElement;
        $arrayQuestion['mensaje']='success';
        }
        else
        {
         $arrayQuestion['mensaje']='Nodata';
        }                    
}
else
{ 
 if($result3['httpCode']==0)
 {$arrayQuestion['mensaje']='Error al conectarse a mercadolibre';}
 else
 {$arrayQuestion['mensaje']= $result3['body']->message;}
}
       

echo json_encode($arrayQuestion);

 ?>
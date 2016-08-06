<?php


session_start('test');
require_once 'libs_php/Mercadilivre/Meli/meli.php';
require_once 'config.php';
$meli = new Meli(APP_ID, APP_KEY);
if(isset($_GET['code']) || isset($_SESSION['access_token'])) {
session_destroy();
  // If code exist and session is empty
  if($_GET['code'] && !(isset($_SESSION['access_token']))) {
    // If the code was in get parameter we authorize
    session_start('test');
    $user = $meli->authorize($_GET['code'], 'http://localhost/ApiMercadolibreAngular/app/index.php');
    // Now we create the sessions with the authenticated user
    $_SESSION['access_token'] = $user['body']->access_token;
    $_SESSION['expires_in'] = time() + $user['body']->expires_in;
    $_SESSION['refresh_token'] = $user['body']->refresh_token;

    if(!isset($_SESSION['userid'])){
          $params = array('access_token' => $_SESSION['access_token'] );
          $result = $meli->get('/users/me', $params);
          if($result['httpCode']==200){
          $_SESSION['first_name']= $result["body"]->first_name;
          $_SESSION['last_name']= $result["body"]->last_name;
          $_SESSION['email_user']= $result["body"]->email;
          $_SESSION['userid']= $result["body"]->id;
            header('Location: main.php'); 
          }else{
             if($result['httpCode']==0){
                          echo "Error al obtener los datos personales Pulsar F5 ";                  
             }
             else{
                        echo $result['body']->message;      
             }       
          }
    }
  } else {
    // We can check if the access token in invalid checking the time
    if($_SESSION['expires_in'] < time()) {
      try {
        // Make the refresh proccess
        $refresh = $meli->refreshAccessToken();

        // Now we create the sessions with the new parameters
        $_SESSION['access_token'] = $refresh['body']->access_token;
        $_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
        $_SESSION['refresh_token'] = $refresh['body']->refresh_token;
      } catch (Exception $e) {
          echo "Exception: ",  $e->getMessage(), "\n";
      }
    }
  }
 
  
 
} else {
 ?>
 <!DOCTYPE html>
<html>
        <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <meta http-equiv="Content-Language" content="es"/>
                <meta name="Description" CONTENT="Author: Carlos Anselmi">
                <title>login-page</title>
        </head>
        <body>
                <div class="login-page">
                        <div class="form">
                                <p class="orioncorp">OrionCorp</p>
                                <form action="profile.html" class="login-form" method="get" accept-charset="utf-8">
                                <?php echo '<a class="button-form" href="'.
                                $meli->getAuthUrl('http://localhost/ApiMercadolibreAngular/app/index.php').'">AUTENTICAR</a>'; ?>
                                <!-- AUTENTICACION DEL CALL-BACK -->
                                </form>
                        </div>  
                </div>
                <p class="center-body">Copyright © 2016  | Carlos Anselmi & Oscar Perez </p>
        </body>

<?php 
}
?>
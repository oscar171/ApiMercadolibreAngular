<?php

    session_start();
   
    $_SESSION['active']=0;
     $_SESSION['sincronizar']=0;
    if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
	}
    session_destroy('test'); 

    header ("Location: http://www.mercadolibre.com.ve/jm/logout"); 
    exit;
?>
 <?php

if ($stream = fopen('http://windowsboys.com.ve/Api2/app/notifica.txt', 'r')) 
{
    // imprimir los 5 primeros bytes
    $result=stream_get_contents($stream);
    echo $result;

    fclose($stream);
}

?>
<?php
require_once '../configuracion.php';
if(!isset($_SESSION)){
session_start();
}

/*librerias de orion corp*/
require_once '../../ltable_olib.php';
require_once '../../Classes/smarty/libs/Smarty.class.php';
require_once '../../plantilla_plugin.php';
require_once '../../producto_class.php';
require_once '../../productos_fichas_fn.php';
require_once '../../plantilla_fn.php';
//END LIBRERIAS ORION CORP

/*LIBRERIAS DE VENEGANGAS PARA AÑADIR PRODUCTO */
include_once '../venegangas/librerias/addProduct.php';

$fo = new lt_form();
$fo->tipo = LT_FORM_DIRECT;
$fo->dbopen();

//incicializamos el objeto de la clase mercadolibre con el id y la clave de la app
include_once '../MercadoLivre/meli.php';
$meli = new Meli(ML_APPID, ML_KEY);
//recibimos por metodo post, todos los productos seleccionados
$idArrays= $_POST['productos_id'];
//recibimos por metodo post, todos las condiciones(nuevo-usado) y tipo de publicacion de los productos seleccionados
$statusArray= $_POST['statusarray'];

$acticulosExitosos=0;
$acticulosFallidos=0;

//ejecutamos mientras existan productos en el array
$band=0;
while (count ($idArrays)>0 and $band!=-1)
{ 
    $idproducto=array_shift($idArrays);

//-------------------------------------VENEGANGAS-------------------------------------------
//------------------VERIFICAR QUE EL PRODUCTO YA FUE AGREGADO ANTERIORMENTE-----------------
    $publicado_mv=0;
    $sql="SELECT producto_id FROM publicaciones_ml WHERE producto_id=".$idproducto;
    if (($q = myquery::q($fo, $sql, 'PUBLI-00')))
    {
        if ($q->sz > 0) $publicado_mv=1;
    }

    /* OBTENER FICHA POR URL
    $opts=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
    $url = "https://orioncorp.com.ve/mprs/plantilla.php?id='".$idproducto."'&mprs_lu=pruebas&mprs_lp=357911";
    $stream= fopen($url,'r');
    if(isset($stream)){
        $descrip= stream_get_contents($stream);
        fclose($stream);
    }else
    { echo "no abrio el url";}
    */
    
    //seleccionamos el prodcuto del array
    //consultamos los datos del producto en la base de datos
    $sql="SELECT id_ficha,descripcion,cod_adux FROM productos WHERE producto_id= ".$idproducto;
    if (($q = myquery::q($fo, $sql, 'PUBLI-01')))
    {
    	$codigogenerado = $q->r->cod_adux;
    	$descrip = plantilla_generar($fo, $idproducto, 1);
 	
	    //obtenemos los capos consulados a la base de datos de cada producto 
    	foreach ($q->a as $x)
	    {
		    //-------START CARGAR IMAGENES---------------------------------------------------
       	    $fotos= array( array("source" => "http://venegangas.com/ml/images/img_".$codigogenerado."_0.jpg"),
	           	array("source" => "http://venegangas.com/ml/images/img_".$codigogenerado."_1.jpg"),
    	        array("source" => "http://venegangas.com/ml/images/img_".$codigogenerado."_2.jpg"),
   	    	    array("source" => "http://venegangas.com/ml/images/img_".$codigogenerado."_3.jpg")
       	    );
		    //END CARGAR IMAGEN

		    //START OBTENER PRECIO
    		$precio = 0;
		    $sql3="SELECT precio_base FROM productos_precios WHERE tienda_id=1 and tipo='A' AND producto_id='".$idproducto."'";
        	if (($q3 = myquery::q($fo, $sql3, 'PUBLI-03')))
        	{
        		$precio = $q3->r->precio_base;
        	}
            else $band=-1;
        	//END OBTENER PRECIO 
    		
    		//START OBTENER CANTIDAD
    		$cantidad = 0;
	        $sql3="SELECT cantidad FROM existencias WHERE almacen_id=105 and producto_id=".$idproducto;
        	if (($q4 = myquery::q($fo, $sql3)))
        	{
    	    	$cantidad=$q4->r->cantidad;
        	}
			else $band=-1;
	    	//END OBTENER CANTIDAD
        
        	$string2 =$x->descripcion;
        
        	//obtenemos el titulo para modificarlo y colocarlo en formato que acepta mercadolibre para realizar la peticion de prediccion de categoria por mercadolibre
        	if ($string2!=NULL)
        	{
            	$title = str_replace(' ', "%20",$string2);
	            $params = array('title'=> $title);
        	    //realizamos la peticion de prediccion de categoria por el titulo
            	$predecirtitle = $meli->get('/sites/MLV/category_predictor/predict',$params) ;
            	if($predecirtitle != NULL) $categoriaPred=$predecirtitle['body']->id;
            	else echo "0";

	            //obtenemos los demas datos de la publicacion
    	        $condicion=array_shift($statusArray);
        	    $tipo_publicacion=array_shift($statusArray);
            	//body de la publicacion, con todos los datos que requiere la publicacion
            	$item = array(
                            "title" => $string2,
                            "category_id" => $categoriaPred,
                            "price" => $precio,
                            "currency_id" => "VEF",
                            "available_quantity" => $cantidad,
                            "buying_mode" => "buy_it_now",
                            "condition" => $condicion,
                            "listing_type_id" =>  $tipo_publicacion,
                            "description" => $descrip,
                            "video_id" => NULL,
                            "warranty" => "90 dias",
                            "pictures" => $fotos
                    );

            	// Realizamos la peticion a mercadolibre de publicar el producto
            	$datosPublicacion = $meli->post('/items', $item, array('access_token' => $_SESSION['access_token']));
            	$venecategoria = $meli->get('/categories/'.$categoriaPred, array('access_token' => $_SESSION['access_token']));
            	$uu = $venecategoria['body']->path_from_root[0]->name;

	            //si la peticion es correcta la agregamos en la base de datos
    	        if ($datosPublicacion['httpCode'] == 201)
    	        {
    	        	$pub = &$datosPublicacion['body'];
    	        	if (($rpub = lt_registro::crear($fo, 'publicaciones_ml', 0, TRUE)))
    	        	{
	    	        	$rpub->av('ID_vendedor', $pub->seller_id);
    		        	$rpub->av('ID_publicacion', $pub->id);
    		        	$rpub->av('titulo_publicacion', $pub->title);
    	    	    	$rpub->av('categoria_publicacion', $pub->category_id);
    	        		$rpub->av('precio_publicacion', $pub->price);
    	        		$rpub->av('cantidad_inicial', $pub->initial_quantity);
	    	        	$rpub->av('cantidad_disponible', $pub->available_quantity);
    		        	$rpub->av('cantidad_vendida', $pub->sold_quantity);
    		        	$rpub->av('fecha_publicada', new lt_fecha($pub->start_time));
    	    	    	$rpub->av('fecha_pausada', new lt_fecha($pub->stop_time));
    	        		$rpub->av('fecha_finalizada', new lt_fecha($pub->end_time));
    	        		$rpub->av('link_permanente', $pub->permalink);
	    	        	$rpub->av('Estatus', 'active');
    		        	$rpub->av('condicion', $condicion);
    		        	$rpub->av('tipoPublicacion', $tipo_publicacion);
    	    	    	$rpub->av('producto_id', $idproducto);
    	        		$rpub->av('producto_codigo', $codigogenerado);
    	        	    	        	
    	        		if ($rpub->guardar())
						{
               				$acticulosExitosos++;
						}
    	        	}
					
//------------------------------------------PRESTASHOP-VENEGANGAS------------------------------------------------------------
//---------------------------------------ANADIR PRODUCTO A VENEGANGAS--------------------------------------------------------
					//addProducto($idproducto,$name,$quantity,$description,$price,$wholesale_price,$reference)
                    try
                    {
                        if ($publicado_mv==0)
                        {
					       addProducto($idproducto,$datosPublicacion['body']->title,$datosPublicacion['body']->available_quantity,
					       		$datosPublicacion['body']->permalink, $datosPublicacion['body']->price, $datosPublicacion['body']->price,
					       		$codigogenerado,$uu);    
                        }
                    }
                    catch (Exception $e)
                    {
                        $trace = $e->getTrace();
                        if ($trace[0]['args'][0] == 404) echo 'Bad ID';
                        else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
                        else echo 'Other error<br />'.$e->getMessage();  
                    }
					// addProducto($idproducto, //ID DEL PRODUCTO CODIGO DEL MPRS  -->>( type : String )
					//             $datosPublicacion['body']->title, //TITULO DE LA PUBLICACION    -->>( type : String )
					//             $datosPublicacion['body']->available_quantity, //CANTIDAD DISPONIBLE    -->>( type : Int o String )
					//             $descrip, //DESCRIPCION DEL PRODUCTO   -->>( type : String )
					//             $datosPublicacion['body']->price, //PRECIO DEL PRODUCTO.    -->>( type : Int o String )
					//             $datosPublicacion['body']->price, //PRECIO AL MAYOR.    -->>( type : Int o String )
					//             'Referenc'); //OBVIO....    -->>( type : Array  )
//---------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------END-PRESTASHOP-VENEGANGAS---------------------------------------------------------

    	        }
        	    else
            	{
                	if($datosPublicacion['httpCode']==0)
                	{
                        echo "error de conexion con mercadolibre";
                        $acticulosFallidos++;
                	}
                 	else
                 	{
                        echo $datosPublicacion['body']->message;
                        echo $datosPublicacion['body']->error;
                        $acticulosFallidos++;
					}
				}
			}
		}
    }
	echo "<pre>";
	echo "Articulos Publicados con exito:". $acticulosExitosos."<br> Arcticulos Fallidos: ".$acticulosFallidos;
	echo "</pre>";
}
?>
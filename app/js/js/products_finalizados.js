
$(".contenedor").off();
       	$(".contenedor").on ('click',".relist",function(){
       
        var id_producto=$(this).closest('tr').attr('id');

        $('.loading').css("display","block");
     
	        
	    $.post("../controlador/republicar_products.php",{


	        producto_id: id_producto

	    }
            ,function(data,status)
	    {

	        if(status)
	        {
	            
	            if(data)
	            {
                    $('.loading').css("display","none"); 
                     $('.alerta').slideDown();
                         $('.texto-alerta').html(data);
                         $(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500); });
	                
	               $(".contenedor").load("../controlador/products_finalizados.php");
	            }
	            else{

	                alert("Ha ocurrido un problema mientras se pausaba la publicacion");
	            }
	        }

	    });

    });

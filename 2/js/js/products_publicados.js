
$(".contenedor").off();
//<----------------------------------------------------->
//Script funcion de el boton modificar 
//redirecciona a una vista donde sale todas las opciones para podificar el producto
   $(".contenedor").on ('click',".modificar",function(){
      //$('.loading').css("display","block");  
        var id_producto=$(this).closest('tr').attr('id');
        alert(id_producto);
        
   $(".contenedor").load("../controlador/modificar_producto_vista.php?producto_id="+id_producto);

   //$('.loading').css("display","none");
        

    });
//<----------------------------------------------------->
//Script funcion de el boton pausar
//si se encuenta activa la publicacion la coloca pausada y cambia su estatus

    $(".contenedor").on ('click',".pausar",function(){


      	$('.loading').css("display","block");
        var id_producto=$(this).closest('tr').attr('id');
             
	    $.post("../controlador/pausar.php",{

	        producto_id: id_producto

	    },function(data,status)
	    {

	        if(status)
	        {
	            
	            if(data)
	            {   $('.loading').css("display","none");
                $('.alerta').slideDown();
                         $('.texto-alerta').html(data);
                         $(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500); });
	               $(".contenedor").load("../controlador/products_publicados.php");
	            }
	            else{

	                alert("Ha ocurrido un problema mientras se pausaba la publicacion");
	            }
	        }

	    });
            
    });
//<----------------------------------------------------->
//Script funcion de el boton reanudar
//si se encuenta pausada la publicacion la coloca activa y cambia su estatus

    $(".contenedor").on ('click',".reanudar",function(){
        
        $('.loading').css("display","block");
        var id_producto=$(this).closest('tr').attr('id');
        
	    $.post("../controlador/pausar.php",{

	        producto_id: id_producto

	    },function(data,status)
	    {

	        if(status)
	        {   
	           
	            if(data)
	            {   
					$('.loading').css("display","none");
					$('.alerta').slideDown();
					$('.texto-alerta').html(data);
					$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});

	                $(".contenedor").load("../controlador/products_publicados.php");
	            }
	            else{
			    		$('.alerta').slideDown();
						$('.texto-alerta').html('Ha ocurrido un problema mientras se pausaba la publicacion');
						$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
	            }
	        }

	    });
            
    });

//<----------------------------------------------------->
//Script funcion de el boton finalizar
//la publicacion la coloca estado finalizada y deja de aparecer en las publicaciones

    $(".contenedor").on ('click',".eliminar",function(){
    	 $('.loading').css("display","block");

        var id_producto=$(this).closest('tr').attr('id');
	        
	    $.post("../controlador/eliminar.php",{

	        
	        producto_id: id_producto

	    },function(data,status)
	    {

	        if(status)
	        {
	            if(data)
	            {   
					$('.loading').css("display","none");
			    	$('.alerta').slideDown();
					$('.texto-alerta').html(data);
					$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
	               $(".contenedor").load("../controlador/products_publicados.php");
	            }
	            else{

	                alert("Ha ocurrido un problema durante la eliminacion");
	            }
	        }

	    });
           

	});

//<----------------------------------------------------->
//Script funcion de el boton pausar seleccionados
//todas las  publicaciones seleccionadas las coloca en estado pausado	

	$(".contenedor").on ('click',".btnPausar",function(){
        var idArray = new Array();

         $('.loading').css("display","block");

				        $("#listado input[type=checkbox]").each(function () {

				        if (this.checked) {
				            
				        idArray.push($(this).val());
				    

				        }
				                
				        });
	      if(jQuery.isEmptyObject(idArray))	{	
				$('.loading').css("display","none");
	    		$('.alerta').slideDown();
				$('.texto-alerta').html('Por favor seleccione una publicacion');
				$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
	    	}else
	    	{

		        
		    $.post("../controlador/pausar_seleccionados.php",{

		        arrayids: idArray

		    },function(data,status)
		    {

		        if(status)
		        {
		            
		            if(data){  
						$('.loading').css("display","none");
			    		$('.alerta').slideDown();
						$('.texto-alerta').html(data);
						$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
		                $(".contenedor").load("../controlador/products_publicados.php");
		            }
		            else{
						$('.loading').css("display","none");
			    		$('.alerta').slideDown();
						$('.texto-alerta').html('Ha ocurrido un problema mientras se pausaba la publicacion');
						$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
		            }
		        }

		    });
	    	}
                  
    });
//<----------------------------------------------------->
//Script funcion de el boton reanudar seleccionados
//todas las  publicaciones seleccionadas las coloca en estado reanudado

	$(".contenedor").on ('click',".btnReanudar",function(){
        	 $('.loading').css("display","block");
	        var idArray = new Array();
			$("#listado input[type=checkbox]").each(function () {

				if (this.checked) 
				{
		        idArray.push($(this).val());
				}
						                
			});
	        
	    if(jQuery.isEmptyObject(idArray))
	    {
			$('.loading').css("display","none");
			$('.alerta').slideDown();
			$('.texto-alerta').html('Por favor seleccione una publicacion');
			$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
	    }else{
	    $.post("../controlador/pausar_seleccionados.php",{

	        arrayids: idArray

	    },function(data,status)
	    {
	        if(status){
	            if(data)
	            {  
                        $('.loading').css("display","none");
			    		$('.alerta').slideDown();
						$('.texto-alerta').html(data);
						$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
	               $(".contenedor").load("../controlador/products_publicados.php");
	            }
	            else{

	                alert("Ha ocurrido un problema mientras se pausaba la publicacion");
	            }
	        }

	    });
	    }
             
    });
//<----------------------------------------------------->
//Script funcion de el boton finalizar seleccionados
//todas las  publicaciones seleccionadas las coloca en estado finalizado

	$(".contenedor").on ('click',".btnFinalizar",function(){
        	 $('.loading').css("display","block");
        var idArray = new Array();

			        $("#listado input[type=checkbox]").each(function () {

			        if (this.checked) 
			        {
			         
			        idArray.push($(this).val());
				  }
			                
			        });
	    if(jQuery.isEmptyObject(idArray))
	    {
			$('.loading').css("display","none");
			$('.alerta').slideDown();
			$('.texto-alerta').html('Por favor seleccione una publicacion');
			$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
	    }else
	    {  
		    $.post("../controlador/finalizar_seleccionados.php",{
			arrayids: idArray
		    },function(data,status){
		        if(status)
		        {
		            if(data)
		            {  
						$('.loading').css("display","none");
						$('.alerta').slideDown();
						$('.texto-alerta').html(data);
						$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
		               $(".contenedor").load("../controlador/products_publicados.php");
		            }
		            else{
						$('.alerta').slideDown();
						$('.texto-alerta').html('Ha ocurrido un problema mientras se pausaba la publicacion');
						$(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500);	});
		            }
		        }

		    });
	    }

             
    });


	//<----------------------------------------------------->
//Script funcion de el checkbox
//Al cambiar de esado el checkbox principal se seleccionan o se desselecciona todos

	$("#marcarTodo").change(function () {
        if ($(this).is(':checked')) {
        //$("input[type=checkbox]").prop('checked', true); //todos los check
        $("#listado input[type=checkbox]").prop('checked', true); //solo los del objeto #diasHabilitados
        } else {
        //$("input[type=checkbox]").prop('checked', false);//todos los check
        $("#listado input[type=checkbox]").prop('checked', false);//solo los del objeto #diasHabilitados
    	}
    	});



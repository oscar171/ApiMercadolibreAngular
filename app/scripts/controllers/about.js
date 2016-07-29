'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('AboutCtrl',['$scope','$http', function ($scope,$http) {
  	
   
    $("#publicar").on('click', function()
    {
        
    //$('.loading').css("display","block");

         //declaramos los vectores que usaremos para guardar los datos

           
    var estatusArray= new Array();
    var idArray = new Array();

        
            //verificamos y guardamos cuales estan seleccionados
        $("#listado input[type=checkbox]").each(function () {

        if (this.checked) {
         if($(this).val()!={}){   
        idArray.push($(this).val());}
        }
                
        });
        alert(idArray);
        //verificamos y guardamos los estados y tipos de publicacion
        $("#listado input[type=radio]").each(function () {

        if (this.checked) {
        if($(this).val()!={}){
        estatusArray.push($(this).val());
        }

        }
                
        });
         alert(estatusArray);
         //verificamos que por lo menos un producto este seleccionado
         //caso contrario mandamos error y no se ejecuta nada
         
        if(jQuery.isEmptyObject(idArray) || jQuery.isEmptyObject(estatusArray) )
        {
                //$('.loading').css("display","none");
        alert("Por favor seleccione una publicacion, Condicion y Tipo de la publicacion");
        

        }else
        {  
                        

        //ejecutamos la sentencia si todos los datos estan correctos
                $.post("../app/controllers/publicar.php",
                {
                productos_id: idArray,
                statusarray : estatusArray
                },
                function(data, status){
                	alert(data);
                        if (status){

                                        if (data)
                                        {

                                                //verificamos si la data a sido enviada y ejecutada correctamente
                                                //cerramos el dialogo de carga
                                                //$('.loading').css("display","none");
                                             //abrimos el dialogo para notificar los acticulos publicados con exito.   
                                                $('.alerta').slideDown();
                                                $('.texto-alerta').html(data);
                                                $(document).ready(function() { setTimeout(function() { $('.alerta').slideUp(); },2500); });
                                                 $("#listado input[type=checkbox]").prop('checked', false);
                                                 $("#listado input[type=radio]").prop('checked', false);

                                                
                                                
                                                    

                                                
                                        }
                                        else    
                                        {

                                            alert (data);
                                        }
                            }else
                                { alert ("error al conectarse la php");}

                
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
  }]);

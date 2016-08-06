
$(document).on ('ready',function (){


    var codigo= $("#varcodigo").val();
    var usuario= $("#nombreuser").val();
     $("#id-user").html(usuario); 

    $("#prod_no_publicados").on('click',function(){

         var usuario= $("#nombreuser").val();
        $("#id-user").html(usuario); 
                     
        $('.contenedor').load("../controlador/products_no_publicados.php");
    });


    $("#prod_finalizados").on('click',function(){
         $("#id-user").html(usuario); 
    $('.contenedor').load("../controlador/products_finalizados.php");
    });

    $("#menu_inicial").on ('click',function()
    {
        location.href="../controlador/logout.php";
    });

     $(".prod_publicados").on ('click',function(){
             $('.contenedor').load("../controlador/products_publicados.php");
     });



});
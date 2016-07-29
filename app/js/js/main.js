


//<----------------------------------------------------->
//Script se cargan variables para usarlas en el nombre de la sesion y el codigo de la sesion

    var codigo= $("#varcodigo").val();
    var usuario= $("#nombreuser").val();
     $("#id-user").html(usuario);
//<----------------------------------------------------->
//Script funcion de el boton productos no publicados
        $("#prod_no_publicados").on('click',function()
        {
        $('.loading').css("display","block");
         var usuario= $("#nombreuser").val();
        $("#id-user").html(usuario); 
        $('.loading').css("display","none");
        $('.contenedor').load("../controlador/products_no_publicados.php");
    });
//<----------------------------------------------------->
//Script funcion de el boton productos finalizados
    $(".prod_finalizados").on('click',function()
    {$('.loading').css("display","block");
         $("#id-user").html(usuario); 
    $('.loading').css("display","none");    
    $('.contenedor').load("../controlador/products_finalizados.php");
    });
 //<----------------------------------------------------->
//Script funcion de el boton productos finalizados
  $(".prod_publicados").on ('click',function(){
    $('.loading').css("display","block");

    $('.contenedor').load("../controlador/products_publicados.php");
    $('.loading').css("display","none");


     });   
//<----------------------------------------------------->
//Script funcion de el boton resumen, y el logo orion corp
    $(".resumen").on ('click',function()
    {
        $('.loading').css("display","block");
        $('.contenedor').load("../controlador/resumen-view.php");
        $('.loading').css("display","none");
    });
       
//<----------------------------------------------------->
//Script funcion de el boton volver
//redirecciona a la pagina principal
$("#menu_inicial").on ('click',function()

    {

        location.href="main-page.php";
    });
//<----------------------------------------------------->
//Script funcion de el boton cerrar sesion
//redirecciona a la pagina de mercadolibre

    $("#logout").on ('click',function()
    {
           

           location.href='../controlador/logout.php';


    });

     // $("#mv-venegangas").on ('click',function(){
     //                // alert("venegangas");
     //      $('.loading').css("display","block");
     //        $('.contenedor').load("../venegangas/index.php");
     // });

    $("#mv-venegangas").on ('click',function(){
         $('.loading').css("display","block");
           $('.contenedor').load("../venegangas/R-Products.php");
     });

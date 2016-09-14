
$(document).on('ready',function () {
    // body...

$("#prueba").on ('click',function(){



$.get("https://www.mercadolibre.com/jms/mlv/lgz/login/authenticate",
       
    function(){
        alert("succes");
        if (status){
        if (data)
            alert(data);

        else {

            alert ("Error al insertar el articulo");
        }
    }else
{ alert ("error al conectarse la php");}



});



});

});
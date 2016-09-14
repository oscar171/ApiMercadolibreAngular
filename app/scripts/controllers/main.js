'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('MainCtrl',['$scope','$http','notify', function ($scope,$http,notify)
{

    //inicializaciamos el scope en limpio, mostramos el loading..
    $scope.listProduct ='';
    $scope.loading=true;
    console.log($scope.loading);

    //hacemos la peticion y sincronizamos los productos de la cuenta
     $http({method: 'GET', url: 'controllers/publicacionesController/sincronizar_productos.php'})
       .success(function(data, status, headers, config)
       {
         if(data.mensaje=="success")
         {
         $scope.loading=false;
         console.log($scope.loading);
         $scope.listProduct=data.data;
         notify("Publicaciones sincronizadas con exito");   
         }
         else
         {
         $scope.loading=false;
         console.log($scope.loading);
         notify("Error al sincronizar, intentelo nuevamente");
         }
       })
     .error(function(data, status, headers, config)
     {
       notify("Ha fallado la petición. Estado HTTP:"+status);
      
     });
     //aplicamos el filtro dependiendo del estatus selecionado
    $scope.orderBY= function(status)
    {$scope.test=status;}
    //Cambio de estados de las publicaciones
    //activo->pausado o finalizado
    //pausado-> activo o finalizado
    //finalizado-> republicar
    $scope.ChangeStatus = function(index) 
    {
    $scope.loading=true;
    console.log($scope.loading);
      switch (index.status)
      {
      case 'active':
          //id: id de la publicacion
          //status: cambio de estatus activo->pausada
          var data =[{status: 'paused' },{id: index.id}];
          $http.post('controllers/publicacionesController/changeStatus.php',data)
          .then(function(response)
          {
            $scope.listProduct.push({'title':response.title } );
            
            if (response.data.mensaje == "success")
            {
            $scope.loading=false;
            console.log($scope.loading);
            index.status='paused';
            index.accion='Activar';
            index.accion2='Finalizar';
            notify("Publicacion Pausada"); 
            }
            else
            {
            $scope.loadialse;
            console.log($scope.loading);
            alert(response.data.mensaje);
            }
          })
               

          break;
          case 'paused':
            //id: id de la publicacion
            //status: cambio de estatus pausado->activo
           var data =[{status: 'active' },{id: index.id}];
           $http.post('controllers/publicacionesController/changeStatus.php',data)
           .then(function(response)
           {
             $scope.listProduct.push({'title':response.title });
             if (response.data.mensaje == "success")
             {
             $scope.loading=false;
             console.log($scope.loading);
             index.status='active';
             index.accion='Pausar';
             index.accion2='Finalizar';
             notify("Publicacion Activada"); 
             }
             else
             {
             $scope.loading=false;
             console.log($scope.loading);
             notify(response.data.mensaje);
             }
           })
            
          break;
          case 'closed':
          //republicar articulo
          //cambia de estatus finalizado-> activo automaticamente
          //id: id de la publicacion
          //listing_type_id: tipo de listado de la publicacion (oro, plata, gratis etc)
          //price: precio de la publicacion
          //title: titulo de la publicacion
          var data =[{id: index.id},{listing_type_id:index.listing_type_id},{price:index.price},{title:index.title}];
          $http.post('controllers/publicacionesController/republicar_products.php',data)
          .then(function(response)
          {
        
            if (response.data.mensaje == "success")
            {
            $scope.loading=false;
            console.log($scope.loading);
            index.id=response.data.nuevoId
            index.status='active';
            index.accion='Pausar';
            index.accion2='Finalizar';
            notify("republicado con exito");
            }
            else
            {
            $scope.loading=false;
            console.log($scope.loading);
            notify(response.data.mensaje);
            }
          }) 
          break;

          default:
            alert("Selected one");
          break; 
      }
    }
    //funcion que finaliza las publicaciones
    //cambia de estado activo o pausado -> finalizado
    $scope.ChangeFinish = function(producto,index)
    {
    $scope.loading=true;
    console.log($scope.loading);
      switch (producto.status)
      {
          case 'active':
          var data =[{status: 'closed' },{id: producto.id}];
          $http.post('controllers/publicacionesController/changeStatus.php',data)
          .then(function(response)
          {
           if (response.data.mensaje == "success")
           {
           $scope.loading=false;
           console.log($scope.loading);
           producto.status='closed';
           producto.accion='Republicar';
           producto.accion2='Eliminar';
           notify("Finalizada con exito"); 
           }
           else
           {
           $scope.loading=false;
           console.log($scope.loading);
           notify(response.data.mensaje);
           }
          })
        

          break;
          case 'paused':
               
          var data =[{status: 'closed' },{id: producto.id}];
          $http.post('controllers/publicacionesController/changeStatus.php',data)
          .then(function(response)
          {
            if (response.data.mensaje == "success")
             {
             $scope.loading=false;
             console.log($scope.loading);
             producto.status='closed';
             producto.accion='Republicar';
             producto.accion2='Eliminar';
             notify("Finalizada con exito"); 
             }
             else
             {
             $scope.loading=false;
             console.log($scope.loading);
             notify(response.data.mensaje);
             }
           })

            
          break;
          case 'closed':

                $scope.loading=false;
                console.log($scope.loading);
                $scope.listProduct.splice(index,1);
          break;

          default:
            alert("Selected one");
            $scope.loading=false;
            console.log($scope.loading);
            
          break;

      }
    }   
//final del controllador main.
}])
  .controller('IndexCtrl',['$scope','$http','$sce','$interval','notify', function ($scope,$http,$sce,$interval,notify){
//Inicio del controllador index
    //inicializamos los scope vacios
    $scope.numNotif='';
    $scope.notificaciones=[];
    //cargamos el nombre y el apellido de la session
    $http({method: 'GET',url: 'controllers/mainController/mainControl.php'})
    .success(function(data, status, headers, config) {
    $scope.firstName=data.firstName;
    $scope.LastName=data.lastName;
      
    }).error(function(data, status, headers, config) {
      alert("Ha fallado la petición. Estado HTTP:"+status);
      
    });
    
    //autorun, verifica si tiene o no nuevas notificaciones el usuario cada 10 segundos
    $interval(function () {
                $scope.nuevanotif();
                }, 10000);
    /*$interval(function () {
                $scope.updateprice();
                }, 10000);*/

    $scope.updateprice= function()
    {
      $http.get('controllers/actualizarPrecios/put.php')
      .then(function(response)
      {
        console.log(response);
        alert(response.data);
                        
        });
        
    }
     
      $scope.nuevanotif= function()
    { 
     $http.get('controllers/procesarNotificaciones.php')
      .then(function(response)
      {
        console.log(response);
              //verificamos si tiene valores el scope notificaciones
          if (response.data.id=="new_order")
             {
                if($scope.notificaciones)
                {
                  
                  $scope.notificaciones.unshift({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/ventas"});
                  $scope.notiClass='notification-counter';
                  $scope.numNotif=parseInt($scope.numNotif+1);

                }
                else
                {
                $scope.notificaciones.push({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/ventas"});
                $scope.notiClass='notification-counter';
                $scope.numNotif=parseInt($scope.numNotif+1);
                
               
               
                }
              }

              if (response.data.id=="new_question")
             {
              if($scope.notificaciones)
               {
                 $scope.notificaciones.unshift({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/preguntas"});
                 $scope.notiClass='notification-counter';
                 $scope.numNotif=parseInt($scope.numNotif+1);   
               }
               else
               {
                 $scope.notificaciones.push({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/preguntas"});
                 $scope.notiClass='notification-counter';
                 $scope.numNotif=parseInt($scope.numNotif+1);
               }
             }

      })
    }


    $scope.remove=function(){  
    $scope.notiClass='';
    $scope.numNotif='';  
    }
  }]);

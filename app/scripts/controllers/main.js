'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('MainCtrl',['$scope','$http', function ($scope,$http) {

    var access_token = '';
    var user_Info = '';
    $scope.listProduct ='';

    $scope.loading=true;
    console.log($scope.loading);

     $http({
     method: 'GET', 
     url: 'controllers/sincronizar_productos.php'
     }).success(function(data, status, headers, config) {
       console.log(data);
       if(data.mensaje=="success"){
     $scope.loading=false;
     console.log($scope.loading);
       $scope.listProduct=data.data;   
     }else{
       $scope.loading=false;
     console.log($scope.loading);
       alert("Error al sincronizar, intentelo nuevamente");
     }
     }).error(function(data, status, headers, config) {
       alert("Ha fallado la petición. Estado HTTP:"+status);
      
     });


    $scope.buscar= function(){
        
        $http({
    method: 'GET', 
    url: 'users//items/search?access_token='+access_token
        }).success(function(data, status, headers, config) {
                $scope.listProduct=[data];
      alert(data.results);
      //$scope.seguro=data;
        }).error(function(data, status, headers, config) {
      alert("Ha fallado la petición. Estado HTTP:"+status);
      
        });

    };

    $scope.orderBY= function(status){

        $scope.test=status;

    }

    

   
    



    $scope.ChangeStatus = function(index) {
     $scope.loading=true;
    console.log($scope.loading);
        switch (index.status) {
          case 'active':

                var data =[{status: 'paused' },{id: index.id}];
                $http.post('controllers/changeStatus.php',data)
                        .then(function(response) {
                             $scope.listProduct.push({'title':response.title } );
                            console.log(response.data.mensaje)
                            if (response.data.mensaje == "success") {
                              $scope.loading=false;
                              console.log($scope.loading);
                                index.status='paused';
                                index.accion='Activar';
                                index.accion2='Finalizar'; 
                            } else {
                              $scope.loading=false;
                              console.log($scope.loading);
                                alert(response.data.mensaje);
                            }
                          })
               

          break;
          case 'paused':
               
               var data =[{status: 'active' },{id: index.id}];
                $http.post('controllers/changeStatus.php',data)
                        .then(function(response) {
                             $scope.listProduct.push({'title':response.title } );
                            console.log(response.data.mensaje)
                            if (response.data.mensaje == "success") {
                              $scope.loading=false;
                              console.log($scope.loading);
                                index.status='active';
                                index.accion='Pausar';
                                index.accion2='Finalizar'; 
                            } else {
                              $scope.loading=false;
                              console.log($scope.loading);
                                alert(response.data.mensaje);
                            }
                    })
            
          break;
          case 'closed':

                var data =[{id: index.id},{listing_type_id:index.listing_type_id},{price:index.price},{title:index.title}];
                $http.post('controllers/republicar_products.php',data)
                        .then(function(response) {
                            console.log(response.data)
                            if (response.data.mensaje == "success") {
                              $scope.loading=false;
                              console.log($scope.loading);
                                index.id=response.data.nuevoId
                                index.status='active';
                                index.accion='Pausar';
                                index.accion2='Finalizar'; 
                            } else {
                              $scope.loading=false;
                              console.log($scope.loading);
                                alert(response.data);
                            }
                    })

              
            
          break;

          default:
            alert("Selected one");
          break;

        }
      }

    $scope.ChangeFinish = function(producto,index) {
      $scope.loading=true;
    console.log($scope.loading);
        switch (producto.status) {
          case 'active':

                var data =[{status: 'closed' },{id: producto.id}];
                $http.post('controllers/changeStatus.php',data)
                        .then(function(response) {
                            console.log(response.data)
                            if (response.data.mensaje == "success") {
                              $scope.loading=false;
                              console.log($scope.loading);
                                producto.status='closed';
                                producto.accion='Republicar';
                                producto.accion2='Eliminar'; 
                            } else {
                              $scope.loading=false;
                              console.log($scope.loading);
                                alert(response.data.mensaje);
                            }
                    })
             

          break;
          case 'paused':
               
               var data =[{status: 'closed' },{id: producto.id}];
                $http.post('controllers/changeStatus.php',data)
                        .then(function(response) {
                            console.log(response.data)
                            if (response.data.mensaje == "success") {
                              $scope.loading=false;
                              console.log($scope.loading);
                                producto.status='closed';
                                producto.accion='Republicar';
                                producto.accion2='Eliminar'; 
                            } else {
                              $scope.loading=false;
                              console.log($scope.loading);
                                alert(response.data.mensaje);
                            }
                    })

            
          break;
          case 'closed':

               /*var data =[{status: 'closed' },{id: producto.id}];
                $http.post('controllers/FinishProduct.php',data)
                        .then(function(response) {
                            console.log(response.data)
                            if (response.data.mensaje == "success") {
                               
                            } else {
                                alert(response.data);
                            }
                    })*/
                $scope.loading=false;
                console.log($scope.loading);
                $scope.listProduct.splice(index,1);
                
                /*index.status='active';
                index.accion='Pausar';
                index.accion2='Finalizar';*/
            
          break;

          default:
            alert("Selected one");
            $scope.loading=false;
             console.log($scope.loading);
            
          break;

        }
      }


   
     

  }])
  .controller('IndexCtrl',['$scope','$http','$sce','$interval','notify', function ($scope,$http,$sce,$interval,notify){

    $scope.numNotif='';
    $scope.notificaciones=[];
    $http({
    method: 'GET', 
    url: 'controllers/mainControl.php'
    }).success(function(data, status, headers, config) {
      $scope.firstName=data.firstName;
      $scope.LastName=data.lastName;
      //alert(data.firstName+data.lastName);
      
    }).error(function(data, status, headers, config) {
      alert("Ha fallado la petición. Estado HTTP:"+status);
      
    });
    /*
    $interval(function () {
                   $scope.nuevanotif();
                }, 10000);*/

      $scope.nuevanotif= function()
    { 
     $http.get('controllers/prueba.php')
      .then(function(response)
      {
          console.log(response);
          if (response.data.id=="new_order")
             {
              console.log(response.data);
              if($scope.notificaciones){

              $scope.notificaciones.unshift({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/ventas"});
              $scope.notiClass='notification-counter';
              $scope.numNotif=parseInt($scope.numNotif+1);
              
              var num1=response.data.data.telefono1;           
              var num2=response.data.data.telefono2;           
              var text="Gracias por su compra hecha por mercadolibre, www.venegangas.com";      
              $http.jsonp('https://www.orioncorp.com.ve/mprs/sms_enviar.php?numero='+num1+'&texto='+text)
              .then(function(response)
                {
                console.log(response);
                })
              if(num2){

                $http.jsonp('https://www.orioncorp.com.ve/mprs/sms_enviar.php?numero='+num2+'&texto='+text)
              .then(function(response)
                {
                console.log(response);
                })
              }
              }
              else
              {
              $scope.notificaciones.push({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/ventas"});
              $scope.notiClass='notification-counter';
              $scope.numNotif=parseInt($scope.numNotif+1);
              
              var num1=response.data.data.telefono1;           
              var num2=response.data.data.telefono2;           
              var text="Gracias por su compra. Registrese aqui www.venegangas.com para continuar con su pedido "+response.data.data.new_order_id+" hecho por mercadolibre";      
              $http.jsonp('https://www.orioncorp.com.ve/mprs/sms_enviar.php?numero='+num1+'&texto='+text)
              .then(function(response)
                {
                console.log(response);
                })
              if(num2){

                $http.jsonp('https://www.orioncorp.com.ve/mprs/sms_enviar.php?numero='+num2+'&texto='+text)
              .then(function(response)
                {
                console.log(response);
                })
              }

              }
              }
              if (response.data.id=="new_question")
             {
              if($scope.notificaciones)
             {
$scope.notificaciones.unshift({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/preguntas"});
              $scope.notiClass='notification-counter';
              $scope.numNotif=parseInt($scope.numNotif+1);   
             }else
             {$scope.notificaciones.push({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/preguntas"});
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

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
    $scope.notificaciones = {
      get : function(index, count, success) {
    var result = [{mensaje:"Te Preguntaron algo",item:"Camisa Manga Larga Tommy",topic:"#/preguntas",thumbnail:"http://mlv-s2-p.mlstatic.com/645721-MLV20829267190_072016-I.jpg"},{mensaje:"Te Preguntaron algo",item:"items2",topic:"#/preguntas",thumbnail:"http://mlv-s2-p.mlstatic.com/645721-MLV20829267190_072016-I.jpg"},{mensaje:"Te compraron algo",item:"items3",topic:"#/ventas",thumbnail:"http://mlv-s2-p.mlstatic.com/645721-MLV20829267190_072016-I.jpg"},{mensaje:"Te compraron algo",item:"items3",topic:"#/ventas",thumbnail:"http://mlv-s2-p.mlstatic.com/645721-MLV20829267190_072016-I.jpg"},{mensaje:"Te compraron algo",item:"items3",topic:"#/ventas",thumbnail:"http://mlv-s2-p.mlstatic.com/645721-MLV20829267190_072016-I.jpg"},{mensaje:"Te compraron algo",item:"items3",topic:"#/ventas",thumbnail:"http://mlv-s2-p.mlstatic.com/645721-MLV20829267190_072016-I.jpg"}];
     index = index <= 0 ? index + 1 : index -1;
    success(result.slice(index, index + count))
      }
    };
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
      $scope.nuevanotif= function()
    {
      
      /*var number='04144378192';
      var text="Gracias por su compra. Registrese aqui www.venegangas.com para continuar con su pedido "+1111111111+" hecho por mercadolibre";      
      $http.jsonp('http://www.orioncorp.com.ve:28703/cgi-bin/sendsms?username=program1&password=43912&to='+number+'&text='+text)
      .then(function(response)
      {
       console.log(response);
      }) */      
     $http.get('controllers/prueba.php')
      .then(function(response)
      {
          console.log(response);
          if (response.data.id=="new_order")
             {
                var num1=response.data.data.telefono1.replace(/[^\d]/g, '');
                var num2=response.data.data.telefono2.replace(/[^\d]/g, '');
                var text="Gracias por su compra. Registrese aqui www.venegangas.com para continuar con su pedido "+response.data.data.new_order_id+" hecho por mercadolibre";      
                $http.jsonp('http://orioncorp.com.ve:28703/cgi-bin/sendsms?username=program1&password=43912&to='+num1+'&text='+text)
                .then(function(response)
                {
                console.log(response);
                }) 
              }
              if (response.data.id=="new_question")
             {
              $scope.notificaciones.push({mensaje:response.data.mensaje,item:response.data.title,thumbnail:response.data.thumbnail,topic:"#/preguntas"});
              $scope.notiClass='notification-counter';
              $scope.numNotif=parseInt($scope.numNotif+1);   
             }
          
      })
      
   }
   $scope.loadMore = function() {
    var last = $scope.notificaciones[$scope.notificaciones.length - 1];
    for(var i = 1; i <= 4; i++) {
      $scope.notificaciones.push(last + i);
    }
  };

    $scope.remove=function(){  
    $scope.notiClass='';
    $scope.numNotif='';  
    }
    $scope.topic='#/preguntas';
    $scope.numNotif=parseInt($scope.numNotif+1);
    $scope.notiClass='notification-counter';
  }]);

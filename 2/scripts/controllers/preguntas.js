'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:NotificacionesCtrl
 * @description
 * # NotificacionesCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('PreguntasCtrl',['$scope','$http','notify', function ($scope,$http,notify) {
    
     $scope.loading=true;
     $scope.title=false;
     $scope.body=false;
    console.log($scope.loading,$scope.title,$scope.body);

    $http.get('controllers/preguntas.php')
        .then(function(response) {
            console.log(response.data)
            
            if (response.data.mensaje == "success") {
              $scope.loading=false;
              $scope.body=true;
              $scope.isCollapsed=true;
              console.log($scope.loading,$scope.body,$scope.isCollapsed);
              

             $scope.listed=response.data.question;
             $scope.total=response.data.total; 
            } else {
              if(response.data.mensaje=="Nodata"){
              $scope.loading=false;
              $scope.title=true;
              console.log($scope.loading,$scope.title);
              }else
              {
                $scope.loading=false;
              console.log($scope.title);
                notify(response.data.mensaje);
              }
            }
    });
    $scope.obtenermas=function()
    {
      $scope.loading=true;
     $scope.title=false;
     $scope.body=false;
    console.log($scope.loading,$scope.title,$scope.body);

    $http.get('controllers/preguntas.php')
        .then(function(response) {
            console.log(response.data)
            
            if (response.data.mensaje == "success") {
              $scope.loading=false;
              $scope.body=true;
              $scope.isCollapsed=true;
              console.log($scope.loading,$scope.body,$scope.isCollapsed);
              

             $scope.listed=response.data.question;
             $scope.total=response.data.total; 
            } else {
              if(response.data.mensaje=="Nodata"){
              $scope.loading=false;
              $scope.title=true;
              console.log($scope.loading,$scope.title);
              }else
              {
                $scope.loading=false;
              console.log($scope.title);
                notify(response.data.mensaje);
              }
            }
    });
    }
    $scope.delete= function(question,index){
      $scope.loading=true;
      $scope.body=false; 
      console.log($scope.loading,$scope.body);
      var data={id:question.idQuestion}
         $http.post('controllers/deleteQuestion.php',data)
                  .then(function(response) {
                   
                    console.log(response.data);
                    if (response.data.mensaje == "success") {
                     $scope.loading=false;
                     $scope.body=true; 
                    console.log($scope.loading,$scope.body); 
                   notify("Pregunta eliminada");
                   $scope.listed.splice(index,1);
              } else
             {
            $scope.loading=false;
            $scope.body=true;
            console.log($scope.loading,$scope.body);
            notify("Error de conexion, por favor intentelo nuevamente");
          }
        })
      
    }    
    $scope.anwers= function(question,modal,index){
      
      var data=[{id:question.idQuestion},{resp:modal.anwers}];
      $scope.loading=true;
      $scope.body=false; 
      console.log($scope.loading,$scope.body);
       $http.post('controllers/responder.php',data)
                  .then(function(response) {
                   
                    console.log(response.data);
                    if (response.data.mensaje == "success") {
                     $scope.loading=false; 
                     $scope.body=true;
                    console.log($scope.loading,$scope.body); 
                   notify("Pregunta respondida con exito");
                   $scope.listed.splice(index,1);
              } else
             {
            $scope.loading=false;
            $scope.body=true;
            console.log($scope.loading,$scope.body);
            notify("Error de conexion, por favor intentelo nuevamente");
          }
        })
               
  
    }



  }]);

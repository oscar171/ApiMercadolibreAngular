'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:NotificacionesCtrl
 * @description
 * # NotificacionesCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('PreguntasCtrl',['$scope','$http','notify', function ($scope,$http,notify)
  {
    //Mostramos le simbolo de cargar y ocultamos el body
     $scope.loading=true;
     $scope.title=false;
     $scope.body=false;
     console.log($scope.loading,$scope.title,$scope.body);

     //carga las preguntas que tiene el vendedor. En caso de no poseer muestra el titulo con el mensaje
    $http.get('controllers/preguntasController/preguntas.php')
    .then(function(response)
    {
      if (response.data.mensaje == "success")
      {
        $scope.loading=false;
        $scope.body=true;
        $scope.isCollapsed=true;
        console.log($scope.loading,$scope.body,$scope.isCollapsed);
        $scope.listed=response.data.question;
        $scope.total=response.data.total; 
      }
      else
      {
        if(response.data.mensaje=="Nodata")
        {
        $scope.loading=false;
        $scope.title=true;
        console.log($scope.loading,$scope.title);
        }
        else
        {
        $scope.loading=false;
        console.log($scope.title);
        notify(response.data.mensaje);
        }
      }
    });
    //hace una peticion para obtener mas preguntas ya que el limite es 5 a la vez
    $scope.obtenermas=function()
    {
     $scope.loading=true;
     $scope.title=false;
     $scope.body=false;
     console.log($scope.loading,$scope.title,$scope.body);

      $http.get('controllers/preguntasController/preguntas.php')
          .then(function(response)
      { if (response.data.mensaje == "success")
       {
       $scope.loading=false;
       $scope.body=true;
       $scope.isCollapsed=true;
       console.log($scope.loading,$scope.body,$scope.isCollapsed);              
       $scope.listed=response.data.question;
       $scope.total=response.data.total; 
       } 
       else 
       {
        if(response.data.mensaje=="Nodata")
        {
        $scope.loading=false;
        $scope.title=true;
        console.log($scope.loading,$scope.title);
        }
        else
        {
        $scope.loading=false;
        console.log($scope.title);
        notify(response.data.mensaje);
        }
       }
      });
    }
    //hace peticion para eliminar la pregunta
    $scope.delete= function(question,index)
    {
      $scope.loading=true;
      $scope.body=false; 
      console.log($scope.loading,$scope.body);
      var data={id:question.idQuestion}
      $http.post('controllers/preguntasController/deleteQuestion.php',data)
      .then(function(response)
      {
       if (response.data.mensaje == "success")
       {
       $scope.loading=false;
       $scope.body=true; 
       console.log($scope.loading,$scope.body); 
       notify("Pregunta eliminada");
       $scope.listed.splice(index,1);
       }
       else
       {
       $scope.loading=false;
       $scope.body=true;
       console.log($scope.loading,$scope.body);
       notify("Error de conexion, por favor intentelo nuevamente");
       }
      })
      
    }
    //hace peticion para responder la pregunta
    //idQuestion : id de la pregunta mostrada
    //resp: valor del texto de la respuesta     
    $scope.anwers= function(question,modal,index)
    {
      
      var data=[{id:question.idQuestion},{resp:modal.anwers}];
      $scope.loading=true;
      $scope.body=false; 
      console.log($scope.loading,$scope.body);
      $http.post('controllers/preguntasController/responder.php',data)
      .then(function(response)
      {
        if (response.data.mensaje == "success")
        {
        $scope.loading=false; 
        $scope.body=true;
        console.log($scope.loading,$scope.body); 
        notify("Pregunta respondida con exito");
        $scope.listed.splice(index,1);
        } 
        else
        {
        $scope.loading=false;
        $scope.body=true;
        console.log($scope.loading,$scope.body);
        notify("Error de conexion, por favor intentelo nuevamente");
        }
      })  
    }



  }]);

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
              console.log($scope.loading,$scope.body);
              

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

    $scope.delete= function(question,index){
      $scope.loading=true; 
      console.log($scope.loading);
      var data={id:question.idQuestion}
         $http.post('controllers/deleteQuestion.php',data)
                  .then(function(response) {
                   
                    console.log(response.data);
                    if (response.data.mensaje == "success") {
                     $scope.loading=false; 
                    console.log($scope.loading); 
                   notify("Pregunta eliminada");
                   $scope.listed.splice(index,1);
              } else
             {
            $scope.loading=false;
            console.log($scope.loading);
            alert(response.data);
          }
        })
      
    }
    $scope.showModal=function(question){

      $(".questions-modal-md").modal("show");
      $scope.question=question.text;
      $scope.id=question.idQuestion;
    };    
    $scope.anwers= function(modal){
      
      $(".questions-modal-md").modal("hide");
      var data=[{id:$scope.id},{resp:modal.anwers}];

      alert(modal.anwers);
      $scope.loading=true; 
      console.log($scope.loading);
       $http.post('controllers/responder.php',data)
                  .then(function(response) {
                   
                    console.log(response.data);
                    if (response.data.mensaje == "success") {
                     $scope.loading=false; 
                    console.log($scope.loading); 
                   notify("Pregunta respondida con exito");
                   $scope.modal.anwers='';
                   // $scope.listProduct.splice(0,1);
              } else
             {
            $scope.loading=false;
            console.log($scope.loading);
            alert(response.data);
          }
        })
               
  
    }



  }]);

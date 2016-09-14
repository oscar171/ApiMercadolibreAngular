'use strict';

/**ReportepreguntasCtrl
 * @ngdoc function
 * @name api2App.controller:ReportepreguntasCtrl
 * @description
 * # ReportepreguntasCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('ReportepreguntasCtrl',['$scope','$http','notify', function ($scope,$http,notify)
  {
    
    var data2=[];
    $scope.data=[];
    $scope.body=false;
    $scope.loading=true;
    console.log($scope.body,$scope.loading);
  		 $http.get('controllers/reportesController/preguntasController.php')
        .then(function(response) 
        {
        	console.log(response);
            if (response.data.mensaje == "success")
            {
              $scope.title=false;
              $scope.body=true;
              $scope.loading=false;
              console.log($scope.title,$scope.body,$scope.loading);
              data2 =response.data.preguntas;
              $scope.data=data2.slice(0,15);
             
            } 
            else
            {
              if(response.data.mensaje=="nodata")
              {
                console.log(response);
              $scope.loading=false;
              console.log($scope.loading);
              }else
              {
                $scope.loading=false;
                notify(response.data.preguntas);
              }
            }
      });

      $scope.getMoreData = function () 
      {
       $scope.data = data2.slice(0, $scope.data.length + 10);
      }
     
  }]);

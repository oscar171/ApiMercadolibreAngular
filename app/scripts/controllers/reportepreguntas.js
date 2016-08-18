'use strict';

/**ReportepreguntasCtrl
 * @ngdoc function
 * @name api2App.controller:ReportepreguntasCtrl
 * @description
 * # ReportepreguntasCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('ReportepreguntasCtrl',['$scope','$http','NgTableParams','notify', function ($scope,$http,NgTableParams,notify) {
    
    $scope.body=false;
    $scope.loading=true;
    console.log($scope.body,$scope.loading);
  		 $http.get('controllers/reportesController/preguntasController.php')
        .then(function(response) 
        {
        	console.log(response.data);
            if (response.data.mensaje == "success") {
              $scope.title=false;
              $scope.body=true;
              $scope.loading=false;
              console.log($scope.title,$scope.body,$scope.loading);
              console.log(response.data);
              var data =response.data.preguntas;
              $scope.tableParams = new NgTableParams({}, { dataset: data});
            console.log(response.data.preguntas);
             
            } else {
              if(response.data.mensaje=="nodata"){
              $scope.loading=false;
              console.log($scope.loading);
              }else
              {
                $scope.loading=false;
                notify(response.data.mensaje);
              }
            }
      });
  }]);

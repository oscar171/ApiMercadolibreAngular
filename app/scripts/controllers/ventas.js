'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:VentasCtrl
 * @description
 * # VentasCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('VentasCtrl',['$scope','$http','NgTableParams','notify', function ($scope,$http,NgTableParams,notify) {
    
    $scope.title=false;
    $scope.body=false;
    $scope.loading=true;
    console.log($scope.title,$scope.body,$scope.loading);
  		  $http.get('controllers/ventasController/ventas.php')
        .then(function(response) 
        { 
          console.log(response);

            if (response.data.mensaje == "success")
            {
              $scope.title=false;
              $scope.body=true;
              $scope.loading=false;
              console.log($scope.title,$scope.body,$scope.loading);
              var data =response.data.order;
              $scope.tableParams = new NgTableParams({}, { dataset: data});
            }
            else 
            {
              if(response.data.mensaje=="nodata")
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
  }]);


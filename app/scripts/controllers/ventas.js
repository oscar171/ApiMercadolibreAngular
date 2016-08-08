'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:VentasCtrl
 * @description
 * # VentasCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('VentasCtrl',['$scope','$http','NgTableParams', function ($scope,$http,NgTableParams) {
    
    $scope.title=false;
    $scope.body=false;
    $scope.loading=true;
    console.log($scope.title,$scope.body,$scope.loading);
  		 $http.get('controllers/ventas.php')
        .then(function(response) {
            console.log(response.data)
            
            if (response.data.mensaje == "success") {
              $scope.title=false;
              $scope.body=true;
              $scope.loading=false;
              console.log($scope.title,$scope.body,$scope.loading);
              console.log(response.data);
              var data =[response.data.order];
              $scope.tableParams = new NgTableParams({}, { dataset: data});
            console.log(response.data.order);
             
            } else {
              if(response.data.mensaje=="nodata"){
              $scope.loading=false;
              $scope.title=true;
              console.log($scope.loading,$scope.title);
              }else
              {
                $scope.loading=false;
                console.log($scope.title);
                alert(response.data.mensaje);
              }
            }
    });
  }]);

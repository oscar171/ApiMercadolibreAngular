'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:VentasCtrl
 * @description
 * # VentasCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('VentasCtrl',['$scope','$http', function ($scope,$http) {
    

  		 $http.get('controllers/ventas.php')
        .then(function(response) {
            console.log(response.data)
            
            if (response.data.mensaje == "success") {
              $scope.title=false;
              $scope.body=true;
              console.log($scope.title,$scope.body);
             $scope.x=response.data.order;
            
             
            } else {
              if(response.data.mensaje=="Nodata"){
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

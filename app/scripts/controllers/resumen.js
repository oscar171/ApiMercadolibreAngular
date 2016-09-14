'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:ResumenCtrl
 * @description
 * # ResumenCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('ResumenCtrl',['$scope','$http','notify', function ($scope,$http,notify)
{

    $scope.loading=true;
    $scope.body=false;
    console.log($scope.loading,$scope.body);
    //sincronizamos el resumen de la cuenta con mercadolibre
    $http({method: 'GET', url: 'controllers/resumenController/resumen-view.php'})
    .success(function(data, status, headers, config)
    {
      if(data.mensaje=='success')
      {
      $scope.loading=false;
      $scope.body=true;
      console.log($scope.loading,$scope.body);
      $scope.activas=data.data.activas;
      $scope.pausadas=data.data.pausadas;
      $scope.finalizadas=data.data.finalizadas;
      $scope.questions=data.data.sinresponder;
      }
      else
      {
      notify(data.mensaje);
      }
      
    })
    .error(function(data, status, headers, config) {
      notify("Ha fallado la petición. Estado HTTP:"+status);
      
    })
}]);

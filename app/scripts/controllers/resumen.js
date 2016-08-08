'use strict';

/**
 * @ngdoc function
 * @name api2App.controller:ResumenCtrl
 * @description
 * # ResumenCtrl
 * Controller of the api2App
 */
angular.module('api2App')
  .controller('ResumenCtrl',['$scope','$http','$interval','notify', function ($scope,$http,$interval,notify) {

    $scope.loading=false;
    $scope.body=true;
    console.log($scope.loading,$scope.body);
      $scope.Timer = null;
            //Timer start function.
            $scope.StartTimer = function () {
                //Set the Timer start message.
                $scope.Message = "Timer started. ";
 
                //Initialize the Timer to run every 1000 milliseconds i.e. one second.
                $scope.Timer = $interval(function () {
                   $scope.enviarsms();
                }, 5000);
            };
 
            //Timer stop function.
            $scope.StopTimer = function () {
 
                //Set the Timer stop message.
                $scope.Message = "Timer stopped.";
 
                //Cancel the Timer.
                if (angular.isDefined($scope.Timer)) {
                    $interval.cancel($scope.Timer);
                }
            }
    /*$http({
    method: 'GET', 
    url: 'controllers/resumen-view.php'
    }).success(function(data, status, headers, config) {
    console.log(data);
    if(data.mensaje=='success'){
     $scope.loading=false;
    $scope.body=true;
    console.log($scope.loading,$scope.body);
      $scope.activas=data.data.activas;
      $scope.pausadas=data.data.pausadas;
      $scope.finalizadas=data.data.finalizadas;
      $scope.questions=data.data.sinresponder;
      }
      else{
        alert(data.mensaje);
      }
      
    }).error(function(data, status, headers, config) {
      alert("Ha fallado la petición. Estado HTTP:"+status);
      
    });*/
}]);

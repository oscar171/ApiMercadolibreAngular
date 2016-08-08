'use strict';

/**
 * @ngdoc overview
 * @name api2App
 * @description
 * # api2App
 *
 * Main module of the application.
 */
angular
  .module('api2App', [
    'ngRoute',
    'cgNotify',
    'ui.bootstrap',
    'ngTable',
    'ui.scroll',
    'ui.scroll.jqlite'
  ])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/publicaciones', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl',
        controllerAs: 'main'
      })
      .when('/publicar', {
        templateUrl: 'controllers/products_no_publicados.php',
        controller: 'AboutCtrl',
        controllerAs: 'About'
      })
      .when('/', {
        templateUrl: 'views/resumen.html',
        controller: 'ResumenCtrl',
        controllerAs: 'resumen'
      })
      .when('/preguntas', {
        templateUrl: 'views/preguntas.html',
        controller: 'PreguntasCtrl',
        controllerAs: 'preguntas'
      })
      .when('/ventas', {
        templateUrl: 'views/ventas.html',
        controller: 'VentasCtrl',
        controllerAs: 'ventas'
      })
      .otherwise({
        redirectTo: '/'
      });
  })
  .directive('loading', function () {
      return {
        restrict: 'E',
        replace:true,
        template: '<div class="loading"><div><div class="c1"></div><div class="c2"></div><div class="c3"></div><div class="c4"></div></div><span>loading</span></div>',
        link: function (scope, element, attr) {
              scope.$watch('loading', function (val) {
                  if (val)
                      $(element).show();
                  else
                      $(element).hide();
              });
        }
      }
  })
 .factory('MyData', function($websocket) {
      // Open a WebSocket connection
      var dataStream = $websocket('ws://windowsboys.com.ve/Api2/app/notificaciones');

      var collection = [];

      dataStream.onMessage(function(message) {
        collection.push(JSON.parse(message.data));
      });

      var methods = {
        collection: collection,
        get: function() {
          dataStream.send(JSON.stringify({ action: 'get' }));
        }
      };

      return methods;
    });

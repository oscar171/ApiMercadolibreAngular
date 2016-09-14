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
    'infinite-scroll'
  ])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/publicaciones', {
        templateUrl: 'views/publicaciones.html',
        controller: 'MainCtrl',
        controllerAs: 'main'
      })
      .when('/publicar', {
        templateUrl: 'controllers/products_no_publicados.php',
        controller: 'productsnopublicados',
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
      .when('/reportePreguntas', {
        templateUrl: 'views/reportepreguntas.html',
        controller: 'ReportepreguntasCtrl',
        controllerAs: 'reportePreguntas'
      })
      .when('/reporteVentas', {
        templateUrl: 'views/reporteventas.html',
        controller: 'ReporteventasCtrl',
        controllerAs: 'reporteVentas'
      })
      .otherwise({
        redirectTo: '/'
      })
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
  });
'use strict';

describe('Controller: ReporteventasCtrl', function () {

  // load the controller's module
  beforeEach(module('api2App'));

  var ReporteventasCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    ReporteventasCtrl = $controller('ReporteventasCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(ReporteventasCtrl.awesomeThings.length).toBe(3);
  });
});

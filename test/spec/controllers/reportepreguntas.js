'use strict';

describe('Controller: ReportepreguntasCtrl', function () {

  // load the controller's module
  beforeEach(module('api2App'));

  var ReportepreguntasCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    ReportepreguntasCtrl = $controller('ReportepreguntasCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(ReportepreguntasCtrl.awesomeThings.length).toBe(3);
  });
});

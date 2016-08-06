  <section ng-controller="NotificacionesCtrl">
    <ul ng-repeat="data in MyData.collection track by $index" >
      <li> {{ data }} </li>
    </ul>
  </section>
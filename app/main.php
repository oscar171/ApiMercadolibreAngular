<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!-- build:css(.) styles/vendor.css -->
    <!-- bower:css -->
    <!-- endbower -->
    <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->

    <!-- endbuild -->
    <!-- build:css(.tmp) styles/main.css -->
<!--     <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
 -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style-menu-navigation.css">
    <link rel="stylesheet" href="css/style-container-ventas.css">
    <link rel="stylesheet" href="css/style-header.css">
    <link rel="stylesheet" href="css/style-footer.css">
    <link rel="stylesheet" href="css/style-load.css">
    <link rel="stylesheet" href="css/style-alerta.css">
    <link rel="stylesheet" href="css/style-section.css">
    <link rel="stylesheet" href="css/bootstrap.min.css" rel="stylesheet">   
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">   
    <link rel="stylesheet" href="bower_components/angular-notify/dist/angular-notify.min.css" rel="stylesheet">   
    <link rel="stylesheet" href="bower_components/ng-table/dist/ng-table.min.css" rel="stylesheet">   
    <!-- START scripts -->
    <!-- START scripts -->
    <script src="bower_components/jquery/dist/jquery.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
    <script src="bower_components/angular-notify/dist/angular-notify.min.js"></script>
    <script src="bower_components/angular-route/angular-route.js"></script>
    <script src="bower_components/angular-animate/angular-animate.js"></script>
    <script src="bower_components/angular-touch/angular-touch.js"></script>
    <script src="bower_components/ui-scroll/dist/ui-scroll.min.js"></script>
    <script src="bower_components/ui-scroll/dist/ui-scroll-jqlite.min.js"></script>
    <script src="js/ui-bootstrap-tpls-2.0.1.min.js"></script>
    <script src="scripts/app.js"></script>
    <script src="scripts/controllers/main.js"></script>
    <script src="scripts/controllers/about.js"></script>
    <script src="scripts/controllers/resumen.js"></script>
    <script src="scripts/controllers/preguntas.js"></script>
    <script src="scripts/controllers/ventas.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>         
    <script src="bower_components/ng-table/dist/ng-table.min.js"></script>         
    <!-- END scripts -->
    <!--<link rel="stylesheet" href="styles/main.css">-->
    <!-- endbuild -->
  </head>
  <body ng-app="api2App" ng-controller="IndexCtrl">
    <!--[if lte IE 8]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Add your site or application content here -->

                    <header class="mv-header">
                       <a ng-href="#/"> <h1 class="pointer resumen">OrionCorp</h1></a>
                        <nav class="mv-navigation">
                                <ul>
                                    <li ng-click='remove()' popover-title="Notificaciones" popover-placement="bottom"
                                        popover-append-to-body="true" popover-trigger="'focus'"
                                        uib-popover-template="'tpl.html'" class="notification-container" tabindex="0" >
                                        <i class="fa fa-globe" ></i>
                                        <span class="{{notiClass}}"">{{numNotif}}</span>
                                        
                                    </li>
                                        <li class="id-user">
                                        <span id="id-user">{{firstName}}{{LastName}}</span>
                                        <span></span>
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        </li>
                                        <li><a href="http://www.mercadolibre.com.ve/" target="_blank">Ir MercadoLibre</a></li>
                                        <li><a  href="http://www.mercadolibre.com.ve/jm/logout" class="pointer">Cerrar Secion</a></li>
                                </ul>
                        </nav>
                </header>
                <button ng-click="nuevanotif()"> Nueva notficiacion</button>
 <script type="text/ng-template" id="tpl.html">

      <div class="popover-content">
        <div ng-click="remove()" class="row">
          <div ui-scroll-viewport style="height:300px">
            <div class="col-xs-12 col-sm-12" ui-scroll="noti in notificaciones" buffer-size='3'>
            <a style="text-decoration:none" ng-href="{{topic}}">
              <div class="col-xs-12 col-sm-12"><strong>{{noti.mensaje}}</strong></div>
              <div class="col-xs-5 col-sm-5" ><img class="img-thumbnail img-responsive" src="{{noti.thumbnail}}" alt=""></div>
              <div class="col-xs-7 col-sm-7">{{noti.item}}</div>
            </a>
            </div>
          </div>
        </div>
      </div> 
    </script>

  <div class="mv-container">
    <nav class='mv-menu-navigation'>
      <ul>
        <li>
            <a ng-href="#/" class="resumen pointer">
              <div class='fa fa-user'></div>
              <span >Resume</span>
            </a>
        </li>
          <li class='sub-menu'>
                <a ng-href="#/publicar"'>
                <div class='fa fa-gear'></div>
                <span>Publicar Productos</span>
                <!--<div class='fa fa-caret-down right'></div>-->
                </a>
          <ul>
            <li>
                  <a ng-href="#/publicaciones" class="pointer prod_publicados" >
                    <span>Publicaciones</span>
                  </a>
            </li>
            <li>
                  <a  ng-href="#/preguntas">
                    <span>Preguntas</span>
                  </a>
            </li>
            <li>
                  <a  ng-href='#/ventas'>
                    <span>Ventas</span>
                  </a>
            </li>
            
          </ul>
          </li>
        <li>
            <a id="mv-venegangas" class="pointer" href="http://venegangas.com/venegangas" target="_blank">
              <div class='fa fa-shopping-bag'></div>
              <span>Venegangas</span>
            </a>
            <li class='sub-menu'>
                <a href="https://www.venegangas.com/venegangas/catalogo/pdf_dinamico" >
                <span>Descargar Catalogo</span>
                <!--<div class='fa fa-caret-down right'></div>-->
                </a>
            </li>
        </li>
      </ul>
    </nav>
    <section class="mv-container-ventas">
    <loading>
   </loading>
        <div ng-view=""> 

        </div> 
    </section>
  </div>

  <footer>
    <!-- Google Analytics: change UA-XXXXX-X to be your site's ID -->
     <script>
       !function(A,n,g,u,l,a,r){A.GoogleAnalyticsObject=l,A[l]=A[l]||function(){
       (A[l].q=A[l].q||[]).push(arguments)},A[l].l=+new Date,a=n.createElement(g),
       r=n.getElementsByTagName(g)[0],a.src=u,r.parentNode.insertBefore(a,r)
       }(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

       ga('create', 'UA-XXXXX-X');
       ga('send', 'pageview');
    </script>
    <!-- build:js(.) scripts/vendor.js -->
    <!-- bower:js -->
    <!-- endbuild -->
  </footer>
</body>
</html>

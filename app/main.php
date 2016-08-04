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
    <!-- START scripts -->
    <script src="bower_components/angular/angular.js"></script>
    <script src="bower_components/angular-route/angular-route.js"></script>
    <script src="bower_components/jquery/dist/jquery.js"></script>
    <script src="bower_components/angular-notify/angular-notify.js"></script>
    <script src="scripts/app.js"></script>
    <script src="scripts/controllers/main.js"></script>
    <script src="scripts/controllers/about.js"></script>
    <script src="scripts/controllers/resumen.js"></script>
    <script src="scripts/controllers/preguntas.js"></script>
    <script src="scripts/controllers/ventas.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>         
    <script src="bower_components/bootstrap/dist/css/bootstrap.css"></script>         
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
                                    <li class="notification-container">
                                        <i class="fa fa-globe"></i>
                                        <span class="notification-counter">10</span>
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

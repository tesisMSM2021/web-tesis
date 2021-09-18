<?php
  session_start();
  $logged = $_SESSION['logged'];

  if(!$logged){
    echo "Ingreso no autorizado";
    die();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require 'php/import-headers.php'; ?>
</head>
<body>
  <div class="app" id="app">
    <!-- ############ LAYOUT START-->
    <!-- BARRA IZQUIERDA -->
    <!-- aside -->
    <div id="aside" class="app-aside modal nav-dropdown">
      <div class="left navside dark dk" data-layout="column">
        <div class="navbar no-radius">
          <!-- brand -->
          <a class="navbar-brand">
            <img src="assets/images/logo.png" alt="." >
            <span class="hidden-folded inline">Tesis MSM</span>
          </a>
          <!-- / brand -->
        </div>
        <div class="hide-scroll" data-flex>
          <nav class="scroll nav-light">

            <ul class="nav" ui-nav>
              <li class="nav-header hidden-folded">
                <small class="text-muted">Main</small>
              </li>
              <li>
                <a href="dashboard.php" >
                  <span class="nav-icon">
                    <i class="fa fa-building-o"></i>
                  </span>
                  <span class="nav-text">Dashboard</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
    <!-- / -->

    <!-- content -->
    <div id="content" class="app-content box-shadow-z0" role="main">
      <div class="app-header white box-shadow">
        <div class="navbar navbar-toggleable-sm flex-row align-items-center">
          <!-- Open side - Naviation on mobile -->
          <a data-toggle="modal" data-target="#aside" class="hidden-lg-up mr-3">
            <i class="material-icons">&#xe5d2;</i>
          </a>
          <!-- Page title - Bind to $state's title -->
          <div class="mb-0 h5 no-wrap" ng-bind="$state.current.data.title" id="pageTitle"></div>

          <!-- BARRA DE LA DERECHA -->
          <ul class="nav navbar-nav ml-auto flex-row">
            <span class="clear hidden-folded p-x mt-2">
              <span class="block _500">tesis2021@gmail.com</span>
            </span>
            <li class="nav-item dropdown">
              <a class="nav-link p-0 clear" href="#" data-toggle="dropdown">
                <span class="avatar w-32">
                  <img src="assets/images/a0.jpg" alt="...">
                  <i class="on b-white bottom"></i>
                </span>
              </a>

            </li>
            <li class="nav-item hidden-md-up">
              <a class="nav-link pl-2" data-toggle="collapse" data-target="#collapse">
                <i class="material-icons">&#xe5d4;</i>
              </a>
            </li>
          </ul>
          <!-- / navbar right -->
        </div>
      </div>

      <!-- PIE DE PAGINA -->
      <div class="app-footer">
        <div class="p-2 text-xs">
          <div class="pull-right text-muted py-1">
            &copy; Copyright <strong>Tesis MSM - 2021</strong>
            <a ui-scroll-to="content"><i class="fa fa-long-arrow-up p-x-sm"></i></a>
          </div>
        </div>
      </div>

      <!-- SECCION CENTRAL -->

      <div ui-view class="app-body" id="view">
          <div class="padding">

            <!-- CARDS CONDICIONES -->

            <div class="row pb-4">
                <div class="col-xs-6 col-sm-4">
                  <ul class="list-group">
                    <li class="list-group-item text-muted">Rangos de Temperatura Ambiente</li>
                    <li class="list-group-item list-group-item-success">Optimo: 16,85 ºC <-> 29,85 ºC</li>
                    <li class="list-group-item list-group-item-warning">Aceptable: 7,85 ºC <-> 39,85 ºC</li>
                    <li class="list-group-item list-group-item-danger">Inaceptable: -0,15 ºC <-> 7,85 ºC  ó  > 40 ºC</li>
                  </ul>
                </div>

                <div class="col-xs-6 col-sm-4">
                  <ul class="list-group">
                    <li class="list-group-item text-muted">Rangos de Humedad Ambiente</li>
                    <li class="list-group-item list-group-item-success">Optimo: 60 % <-> 80 %</li>
                    <li class="list-group-item list-group-item-warning">Aceptable: 60 % <-> 80 %</li>
                    <li class="list-group-item list-group-item-danger">Inaceptable: 0 % <-> 59% ó 80 % <-> 100%</li>
                  </ul>
                </div>

                <div class="col-xs-6 col-sm-4">
                  <ul class="list-group">
                    <li class="list-group-item text-muted">Rangos de Humedad de Suelo</li>
                    <li class="list-group-item list-group-item-success">Optimo: 50 % <-> 70 %</li>
                    <li class="list-group-item list-group-item-warning">Aceptable: 60 % ºC <-> 85 %</li>
                    <li class="list-group-item list-group-item-danger">Inaceptable: 0 % <-> 30 %  ó  85 % <-> 100 %</li>
                  </ul>
                </div>
            </div>

            <!-- VALORES EN TIEMPO REAL -->

            <div class="row pb-4">
              <div class="col-xs-6 col-sm-4">
                <div class="box p-a">
                  <div class="pull-left m-r">
                    <span class="w-48 rounded primary">
                      <img src="assets/images/temp.svg" width="28px">
                    </span>
                  </div>
                  <div class="clear">
                    <h4 class="m-0 text-lg _300"><b id="display_temp">-- </b><span class="text-sm"> ºC</span></h4>
                    <small class="text-muted">Temperatura Ambiente</small>
                  </div>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4">
                <div class="box p-a">
                  <div class="pull-left m-r">
                    <span class="w-48 rounded  accent">
                      <img src="assets/images/hum.svg" width="28px">
                    </span>
                  </div>
                  <div class="clear">
                    <h4 class="m-0 text-lg _300"><b id="display_hum">-- </b><span class="text-sm"> %</span></h4>
                    <small class="text-muted">Humedad Ambiente</small>
                  </div>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4">
                <div class="box p-a">
                  <div class="pull-left m-r">
                    <span class="w-48 rounded warn">
                      <img src="assets/images/soilhum.svg" width="32px">
                    </span>
                  </div>
                  <div class="clear">
                    <h4 class="m-0 text-lg _300">
                      <b id="display_hum_soil">-- </b><span class="text-sm"> %</span>
                    </h4>
                    <small class="text-muted">Humedad de suelo</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- RANGO ACTUAL -->

            <div class="row">
                <div class="col-xs-6 col-sm-4">
                    <div id="tempOP" class="alert alert-success" role="alert">
                      La temperatura se encuentra en un rango optimo.
                    </div>
                    <div id="tempAC" class="alert alert-warning" role="alert">
                      La temperatura se encuentra en un rango aceptable.
                    </div>
                    <div id="tempIN" class="alert alert-danger" role="alert">
                      La temperatura se encuentra en un rango inaceptable.
                    </div>
                </div>

                <div class="col-xs-6 col-sm-4">
                  <div id="humOP" class="alert alert-success" role="alert">
                    La humedad se encuentra en un rango optimo.
                  </div>
                  <div id="humAC" class="alert alert-warning" role="alert">
                    La humedad se encuentra en un rango aceptable.
                  </div>
                  <div id="humIN" class="alert alert-danger" role="alert">
                    La humedad se encuentra en un rango inaceptable.
                  </div>
                </div>

                <div class="col-xs-6 col-sm-4">
                  <div id="humSOP" class="alert alert-success" role="alert">
                    La humedad de suelo se encuentra en un rango optimo.
                  </div>
                  <div id="humSAC" class="alert alert-warning" role="alert">
                    La humedad de suelo se encuentra en un rango aceptable.
                  </div>
                  <div id="humSIN" class="alert alert-danger" role="alert">
                    La humedad de suelo se encuentra en un rango inaceptable.
                  </div>
                </div>
            </div>

            <!-- ALERTAS ACTUADORES-->

            <div class="row pb-4">
                <div class="col-xs-6 col-sm-4">
                    <div id="alertVentana" class="alert alert-danger" role="alert">
                        La ventana esta abierta.
                    </div>
                </div>
                <div class="col-xs-6 col-sm-4">
                    <div id="alertVentilacion" class="alert alert-danger" role="alert">
                        La Ventilación esta encendida.
                    </div>
                </div>
                <div class="col-xs-6 col-sm-4">
                    <div id="alertRiego" class="alert alert-danger" role="alert">
                        El riego esta encendido.
                    </div>
                </div>
            </div>

            <!-- ALERTAS ACTUADORES-->

            <div class="row pb-4">
                <div class="col-xs-6 col-sm-4">
                    <div id="alertVentanaManual" class="alert alert-success" role="alert">
                      <div class="clear">
                        <h4 class="m-0 text-lg _300"><div id="ten-countdown"></div></h4>
                      </div>
                        La ventana se abrió manualmente. <br>
                        Por seguridad se cerrará a los 10 minutos.
                    </div>
                </div>
                <div class="col-xs-6 col-sm-4">
                    <div id="alertVentilacionManual" class="alert alert-success" role="alert">
                      La ventilación se encendió manualmente. <br>
                      Por seguridad se apagará a los 10 minutos.
                    </div>
                </div>
                <div class="col-xs-6 col-sm-4">
                    <div id="alertRiegoManual" class="alert alert-success" role="alert">
                      El riego se encendió manualmente. <br>
                      Por seguridad se apagará a los 10 minutos.
                    </div>
                </div>
            </div>

            <!-- SWItCHES -->

            <div class="row pb-4">
              <!-- SWItCH1 -->
              <div class="col-xs-12 col-sm-4">
                <div class="box p-a" id="box-ventana">
                  <div class="form-group row">
                    <label class="col-sm-6 form-control-label text-muted">Abrir/cerrar ventana</label>
                    <div class="col-sm-6">
                      <label class="ui-switch ui-switch-md info m-t-xs">
                        <input id="input_servo" onchange="switch_ventana()"  type="checkbox" >
                        <i></i>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

                <!-- SWItCH2 -->
                <div class="col-xs-12 col-sm-4">
                  <div class="box p-a" id="box-cooler">
                    <div class="form-group row">
                      <label class="col-sm-8 form-control-label text-muted">Prender/apagar ventilación</label>
                      <div class="col-sm-4">
                        <label class="ui-switch ui-switch-md info m-t-xs">
                          <input id="input_cooler" onchange="switch_cooler()" type="checkbox"  >
                          <i></i>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- SWItCH3 -->
                <div class="col-xs-12 col-sm-4">
                  <div class="box p-a" id="box-riego">
                    <div class="form-group row">
                      <label class="col-sm-6 form-control-label text-muted">Prender/apagar riego</label>
                      <div class="col-sm-6">
                        <label class="ui-switch ui-switch-md info m-t-xs">
                          <input id="input_bomba" onchange="switch_bomba()" type="checkbox"  >
                          <i></i>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <!-- GRAFICOS -->

            <div class="row">
              <div class="col-xs-12 col-sm-6 mt-1">
                <div class="box">
                  <!-- GRAFICO TEMPERATURA -->
                  <div class="box-header">
                    <h3>Reporte temperatura</h3>
                    <small class="block text-muted">Úlimas 10 mediciones de temperatura ambiente.</small>
                  </div>
                  <div class="box-body">
                    <div>
                      <canvas id="chartTemp"></canvas>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 mt-1">
              <div class="box">
                <!-- GRAFICO TEMPERATURA -->
                <div class="box-header">
                  <h3>Reporte humedad</h3>
                  <small class="block text-muted">Úlimas 10 mediciones de humedad ambiente.</small>
                </div>
                <div class="box-body">
                  <div>
                    <canvas id="chartHum"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>

            <div class="row mt-3">
              <div class="col-xs-12 col-sm-6 mt-1">
                <!-- GRAFICO HUM SUELO -->
                <div class="box">
                  <div class="box-header">
                    <h3>Reporte humedad de suelo</h3>
                    <small class="block text-muted">Úlimas 10 mediciones de humedad de suelo.</small>
                  </div>
                  <div class="box-body">
                    <div>
                      <canvas id="chartHumSoil"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-sm-6 mt-1">

              </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SECCION CENTRAL -->

    <!-- SELECTOR DE TEMAS -->
    <div id="switcher">
     <div class="switcher box-color dark-white text-color" id="sw-theme">
       <a href ui-toggle-class="active" target="#sw-theme" class="box-color dark-white text-color sw-btn">
         <i class="fa fa-gear"></i>
       </a>
       <div class="box-divider"></div>
       <div class="box-body">
         <p>Themes:</p>
         <div data-target="bg" class="row no-gutter text-u-c text-center _600 clearfix">
           <label class="p-a col-sm-6 light pointer m-0">
             <input type="radio" name="theme" value="" hidden>
             Light
           </label>
           <label class="p-a col-sm-6 grey pointer m-0">
             <input type="radio" name="theme" value="grey" hidden>
             Grey
           </label>
           <label class="p-a col-sm-6 dark pointer m-0">
             <input type="radio" name="theme" value="dark" hidden>
             Dark
           </label>
           <label class="p-a col-sm-6 black pointer m-0">
             <input type="radio" name="theme" value="black" hidden>
             Black
           </label>
         </div>
       </div>
     </div>
    </div>
  </div>

    <?php require 'php/import-script.php' ?>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script src="./js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/graficos/indexChart.js"></script>
</body>
</html>

<?php
require_once('../backend/db/config.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>Centro | Medico</title>
  <meta name="description" content="Login Page" />
  <!-- Favicon Tags Start -->
  <link rel="apple-touch-icon-precomposed" sizes="57x57" href="img/favicon/logo1.png" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/favicon/logo1.png" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/favicon/logo1.png" />
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="img/favicon/logo1.png" />
  <link rel="apple-touch-icon-precomposed" sizes="60x60" href="img/favicon/logo1.png" />
  <link rel="apple-touch-icon-precomposed" sizes="120x120" href="img/favicon/logo1.png" />
  <link rel="apple-touch-icon-precomposed" sizes="76x76" href="img/favicon/logo1.png" />
  <link rel="apple-touch-icon-precomposed" sizes="152x152" href="img/favicon/logo310.png" />
  <link rel="icon" type="image/png" href="img/favicon/logo1.png" sizes="196x196" />
  <link rel="icon" type="image/png" href="img/favicon/logo1.png" sizes="96x96" />
  <link rel="icon" type="image/png" href="img/favicon/logo1.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="img/favicon/logo16.png" sizes="16x16" />
  <link rel="icon" type="image/png" href="img/favicon/logo1.png" sizes="128x128" />
  <meta name="application-name" content="&nbsp;" />
  <meta name="msapplication-TileColor" content="#FFFFFF" />
  <meta name="msapplication-TileImage" content="img/favicon/logo1.png" />
  <meta name="msapplication-square70x70logo" content="img/favicon/logo1.png" />
  <meta name="msapplication-square150x150logo" content="img/favicon/logo1.png" />
  <meta name="msapplication-wide310x150logo" content="img/favicon/logo310.png" />
  <meta name="msapplication-square310x310logo" content="img/favicon/logo310.png" />
  <!-- Favicon Tags End -->
  <!-- Font Tags Start -->
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="font/CS-Interface/style.css" />
  <!-- Font Tags End -->
  <!-- Vendor Styles Start -->
  <link rel="stylesheet" href="css/vendor/bootstrap.min.css" />
  <link rel="stylesheet" href="css/vendor/OverlayScrollbars.min.css" />

  <!-- Vendor Styles End -->
  <!-- Template Base Styles Start -->
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Template Base Styles End -->

  <link rel="stylesheet" href="css/main.css" />
  <script src="js/base/loader.js"></script>
</head>

<body class="h-100">
  <div id="root" class="h-100">
    <!-- Background Start -->
    <div class="fixed-background"></div>
    <!-- Background End -->

    <div class="container-fluid p-0 h-100 position-relative">
      <div class="row g-0 h-100">
        <!-- Left Side Start -->
        <div class="offset-0 col-12 d-none d-lg-flex offset-md-1 col-lg h-lg-100">
          <div class="min-h-100 d-flex align-items-center">
            <div class="text-center">
              <img src="img/favicon/logo310.png" alt="Descripción de la imagen">
            </div>
            <div class="w-100 w-lg-75 w-xxl-50">
              <div>
                <div class="mb-5">
                  <h1 class="display-3 text-white">Centro Medico</h1>
                </div>
                <p class="h6 text-white lh-1-5 mb-5">
                  En nuestro hospital, nos dedicamos a ofrecer atención médica de excelencia, poniendo en el centro de todo a nuestros pacientes.
                  Con un equipo de profesionales altamente capacitados y tecnología de vanguardia...
                </p>
                <div class="mb-5">
                  <!--<a class="btn btn-lg btn-outline-white" href="index.html">Mas...</a>-->
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Left Side End -->


        <!-- Right Side Start -->
        <div class="col-12 col-lg-auto h-100 pb-4 px-4 pt-0 p-lg-0">
          <div class="sw-lg-70 min-h-100 bg-foreground d-flex justify-content-center align-items-center shadow-deep py-5 full-page-content-right-border">
            <div class="sw-lg-50 px-5">
              <div class="sh-11">
                <a href="index.html">
                  <div class="logo-default"></div>
                </a>
              </div>
              <div class="mb-5">
                <h2 class="cta-1 mb-0 text-primary">Bienvenido,</h2>
                <h2 class="cta-1 text-primary">¡empecemos!</h2>
                <div id="error-message"></div>
              </div>
              <div class="mb-5">
                <p class="h6">Utilice sus credenciales para iniciar sesión.</p>
                <p class="h6">
                  Si no es miembro, por favor
                  <a href="Pages.Authentication.Register.html">Registrarse</a>.
                </p>
              </div>
              <div id="iconsNotify">
                <form id="loginForm" class="tooltip-end-bottom" novalidate>
                  <div class="mb-3 filled form-group tooltip-end-top">
                    <i data-acorn-icon="email"></i>
                    <input class="form-control" placeholder="Email" name="email" />
                  </div>
                  <div class="mb-3 filled form-group tooltip-end-top">
                    <i data-acorn-icon="lock-off"></i>
                    <input class="form-control pe-7" name="password" type="password" placeholder="Password" />
                    <a class="text-small position-absolute t-3 e-3" href="Pages.Authentication.ForgotPassword.html">Olvido Contraseña?</a>
                  </div>
                  <button type="submit" class="btn btn-lg btn-primary">Iniciar Sesion</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Right Side End -->
      </div>
    </div>
  </div>

  <!-- Vendor Scripts Start -->
  <script src="js/vendor/jquery-3.5.1.min.js"></script>
  <script src="js/vendor/bootstrap.bundle.min.js"></script>
  <script src="js/vendor/OverlayScrollbars.min.js"></script>
  <script src="js/vendor/autoComplete.min.js"></script>
  <script src="js/vendor/clamp.min.js"></script>

  <script src="icon/acorn-icons.js"></script>
  <script src="icon/acorn-icons-interface.js"></script>

  <script src="js/vendor/jquery.validate/jquery.validate.min.js"></script>

  <script src="js/vendor/jquery.validate/additional-methods.min.js"></script>
  <script src="js/vendor/bootstrap-notify.min.js"></script>
  <script src="js/cs/scrollspy.js"></script>


  <!-- Vendor Scripts End -->

  <!-- Template Base Scripts Start -->
  <script src="js/base/helpers.js"></script>
  <script src="js/base/globals.js"></script>
  <script src="js/base/nav.js"></script>
  <script src="js/base/search.js"></script>
  <script src="js/base/settings.js"></script>
  <!-- Template Base Scripts End -->
  <!-- Page Specific Scripts Start -->

  <script src="js/pages/auth.login.js"></script>
  <script src="js/components/toasts.js"></script>
  <script src="js/plugins/notifies.js"></script>

  <script src="js/common.js"></script>
  <script src="js/scripts.js"></script>
  <!-- Page Specific Scripts End -->
</body>

</html>
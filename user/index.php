<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  $temp_return_page = "";
  if (isset($_GET["RET"]) && isset($_POST["RET"])){
    $_SESSION["returnPage"] = $_GET["RET"];
  }
  session_cache_limiter(null);
  $tab = "user";
  $nav = "userlogin";
  $helpPage = "user";
  $focus_form_name = "loginform";
  $focus_form_field = "username";
 // require_once("../shared/get_form_vars.php");
//  $focus_form_name = "barcodesearch";
//  $focus_form_field = "searchText";
  // require_once("../shared/logincheck.php");
  //require_once("../opac/header_opac.php");
  include("../opac/header_top_opac.php");
  //include("../user/navbar_login.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

    $pageErrors = $_SESSION["pageErrors"];
    $error = $pageErrors["pass_user"];

  if ("$error" != "") {
    $login = "item active";
    $titulo = "item";
  } else {
    $titulo = "item active";
    $login = "item";
  }
?>
<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<script type="text/javascript" ">
   function Validar(f) {
    if (f.searchText.value=='') {
      alert("Por favor Digite almenos Un digito del codigo / una letra del apellido ");
      f.searchText.focus();
      return (false);
    }
   }
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>


<script type="text/javascript">
  $(document).ready(function() {
  var delta = 0;
  var scrollThreshold = 2;

  // detect available wheel event
  wheelEvent = "onwheel" in document.createElement("div") ? "wheel" : // Modern browsers support "wheel"
      document.onmousewheel !== undefined ? "mousewheel" :         // Webkit and IE support at least "mousewheel"
      "DOMMouseScroll";                                            // let's assume that remaining browsers are older Firefox

  // Bind event handler
  $(window).on(wheelEvent, function (e) {
      // Do nothing if we weren't scrolling the carousel
      var carousel = $('.carousel.vertical:hover');
      if (carousel.length === 0)  return;

      // Get the scroll position of the current slide
      var currentSlide = $(e.target).closest('.item')
      var scrollPosition = currentSlide.scrollTop();

      // --- Scrolling up ---
      if (e.originalEvent.detail < 0 || e.originalEvent.deltaY < 0 || e.originalEvent.wheelDelta > 0) {
          // Do nothing if the current slide is not at the scroll top
          if(scrollPosition !== 0) return;

          delta--;

          if ( Math.abs(delta) >= scrollThreshold) {
              delta = 0;
              carousel.carousel('prev');
          }
      }

      // --- Scrolling down ---
      else {
          // Do nothing if the current slide is not at the scroll bottom
          var contentHeight = currentSlide.find('> .content').outerHeight();
          if(contentHeight > currentSlide.outerHeight() && scrollPosition + currentSlide.outerHeight() !== contentHeight) return;

          delta++;
          if (delta >= scrollThreshold)
          {
              delta = 0;
              carousel.carousel('next');
          }
      }

      // Prevent page from scrolling
      return false;
  });
})
</script>


<script type="text/javascript">
  $(document).ready(function() {
    $('#main-content').fadeIn();
});
</script>

    <link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet">

<div id="main-content">
  <div class="carousel slide vertical" id="carousel-vertical" data-ride="carousel" data-interval="false">
    <ol class="carousel-indicators">
    <!--  <li data-target="#carousel-vertical" data-slide-to="0" class="active"></li>
      <li data-target="#carousel-vertical" data-slide-to="1"></li>-->
    </ol>
    <div class="carousel-inner" role="listbox">
      <div class="<?php echo "$titulo"; ?>">
        <div class="row" id="titulo">
        <p style="font-size: 55">Biblioteca Facultad Regional Delta</p>
        <p>Universidad Tecnológica Nacional</p>

        <button class="btn-iniciar btn btn-primary" data-target="#carousel-vertical" data-slide-to="1">Iniciar sesión</button>
        </div>
      </div>
      <div class="<?php echo "$login"; ?>">

        <form name="loginform" method="POST" action="../user/login.php" onsubmit="return Validar(this);">

                      <div class="row">
                          <div class="Absolute-Center is-Responsive">
                            <div class="form-top">
                              <div class="form-top-left">
                                <h3 style="color: #fff; font-family: Roboto">Iniciar sesión</h3>
                              </div>
                              <div class="form-top-right">
                                <i class="fa fa-key"></i>
                              </div>
                            </div>
                              <div class="form-bottom">
                                <form role="form" action="" method="post" class="login-form">
                                  <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png">
                                  <div class="form-group">
                                    <label class="sr-only" for="form-username">Número de tarjeta</label>
                                      <input class="form-control" type="user" name="barcode_nmbr" placeholder="Número de tarjeta" value="<?php if (isset($postVars["barcode_nmbr"])) echo H($postVars["barcode_nmbr"]); ?>" >
                                      <font><?php if (isset($pageErrors["barcode_nmbr"])) echo H($pageErrors["barcode_nmbr"]); ?></font>
                                  </div>
                                  <div class="form-group">
                                      <label class="sr-only" for="form-password">Contraseña</label>
                                       <input class="form-control" type="password" name="pass_user" placeholder="Contraseña" value="<?php if (isset($postVars["pass_user"])) echo H($postVars["pass_user"]); ?>" >
                                         <?php if (isset($pageErrors["pass_user"])) echo H($pageErrors["pass_user"]); ?></font>
                                  </div>
                                    <button id="btnlogin" type="submit" class="btn btn-primary"><?php echo $loc->getText("loginFormLogin"); ?></button>
                                </form>
                              </div>
                          </div>
                      </div>
        </form>

      </div>
    </div>
  </div>
</div>

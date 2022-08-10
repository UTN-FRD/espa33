<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "shared";
  $nav = "";
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header_top_login.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  if (isset($_SESSION["pageErrors"])){
    $pageErrors = $_SESSION["pageErrors"];
    $error = "";
    if (isset($pageErrors["username"])) $error = $pageErrors["username"];
    if (isset($pageErrors["pwd"])) $error = $pageErrors["pwd"];
  } else {
    $error = "";
  }
  
  if ($error != "") {
    $login = "item active";
    $titulo = "item";
  } else {
    $titulo = "item active";
    $login = "item";
  }

  $temp_return_page = "";
  if (isset($_GET["RET"])){
    if (in_array($_GET["RET"], $pages, true)) {
      $_SESSION["returnPage"] = $_GET["RET"];
    } else {
      $_SESSION["returnPage"] = '../home/index.php';
    }
  }

?>

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

<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet">
<link rel="stylesheet" href="../css/Material/Material.min.css">
<script src="../css/Material/material.min.js"></script>

<div class="carousel slide vertical" id="carousel-vertical" data-ride="carousel" data-interval="false">
  <ol class="carousel-indicators">
  <!--  <li data-target="#carousel-vertical" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-vertical" data-slide-to="1"></li>-->
  </ol>
  <div class="carousel-inner" role="listbox">
    <div class="<?php echo "$titulo"; ?>">
      <div class="row" id="titulo">
        <div style="font-size: 55">Biblioteca Facultad Regional Delta</div>
        <div style="margin-top: 10px">Universidad Tecnológica Nacional</div>
        <button style="font-size: 17px; margin-top: 50px; color: #ffffff;" class="btn-iniciar mdl-button mdl-js-button mdl-js-ripple-effect" data-target="#carousel-vertical" data-slide-to="1">Iniciar sesión</button>
      </div>
    </div>
    <div class="<?php echo "$login"; ?>">
    <form name="loginform" method="POST" action="../shared/login.php">  
      <div class="row">
        <div class="Absolute-Center is-Responsive">
          <div class="form-top">
            <div class="form-top-left">
              <h4 style="color: #000; font-family: Roboto"><?php echo $loc->getText("loginFormTbleHdr"); ?></h4>
            </div>
            <div class="form-top-right">
              <i class="fa fa-key"></i>
            </div>
          </div>
          <div class="form-bottom">
            <form role="form" action="" method="post" class="login-form">
              <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png">
              <div class="form-group">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php if (isset($pageErrors["username"])) echo "is-invalid"; ?> ">
                  <input class="mdl-textfield__input" style="box-shadow:  0 0 0 30px white inset" id="username" type="text" name="username" autocomplete="username" value="<?php if (isset($postVars["username"])) echo H($postVars["username"]); ?>">
                  <div class="mdl-textfield__label" for="form-username"><?php echo $loc->getText("loginFormUsername"); ?></div>
                  <?php if (isset($pageErrors["username"])) echo "<span class='mdl-textfield__error'>" . $pageErrors["username"] . "</span>"; ?> 
                </div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label <?php if (isset($pageErrors["pwd"])) echo "is-invalid"; ?> ">
                  <input class="mdl-textfield__input" style="box-shadow:  0 0 0 30px white inset" id="password" type="password" name="password" value="<?php if (isset($postVars["pwd"])) echo H($postVars["pwd"]); ?>">
                  <div class="mdl-textfield__label" for="form-password"><?php echo $loc->getText("loginFormPassword"); ?></div>
                  <?php if (isset($pageErrors["pwd"])) echo "<span class='mdl-textfield__error'>" . $pageErrors["pwd"] . "</span>"; ?>                                   
                </div> 
                <button style="width: 100%; vertical-align: middle; margin-top: 10px;" type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent"><?php echo $loc->getText("loginFormLogin"); ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script type="text/javascript">
  
setTimeout(function () {

  var autofilled = document.querySelectorAll('input#password:-webkit-autofill');
  
  if (autofilled) {
    $("input[type=password]").parent().addClass("is-dirty");
    $("input[type=text]").parent().addClass("is-dirty");
  }

}, 

300);

</script>

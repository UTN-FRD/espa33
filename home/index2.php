<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "home";
  $nav = "home";

  require_once("../shared/get_form_vars.php");
  require_once("../shared/header_top_index.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

    $temp_return_page = "";
  if (isset($_GET["RET"])){
    $_SESSION["returnPage"] = $_GET["RET"];
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


    <div class="carousel slide vertical" id="carousel-vertical" data-ride="carousel" data-interval="false">
  <ol class="carousel-indicators">
  <!--  <li data-target="#carousel-vertical" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-vertical" data-slide-to="1"></li>-->
  </ol>
  <div class="carousel-inner" role="listbox">
    <div class="item active">

    <div class="row" id="titulo">
    <p style="font-size: 55">Biblioteca Facultad Regional Delta</p>
    <p>Universidad Tecnológica Nacional</p>

    <button class="btn-iniciar btn btn-primary" data-target="#carousel-vertical" data-slide-to="1">Iniciar sesión</button>
    </div>

    </div>
    <div class="item">

    <form name="loginform" method="POST" action="../shared/login.php">  
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
                              <label class="sr-only" for="form-username">Username</label>
                                <input class="form-control" type="user" name="username"  placeholder="Usuario" value="<?php if (isset($postVars["username"])) echo H($postVars["username"]); ?>" >

                                <font class="error"><?php if (isset($pageErrors["username"])) echo H($pageErrors["username"]); ?></font>

                              </div>
                              <div class="form-group">
                                <label class="sr-only" for="form-password">Password</label>
                                 <input class="form-control" type="password" placeholder="Contraseña" name="pwd"value="<?php if (isset($postVars["pwd"])) echo H($postVars["pwd"]); ?>" >

                                  <?php if (isset($pageErrors["pwd"])) echo H($pageErrors["pwd"]); ?></font>

                              </div>
                              <button id="btnlogin" type="submit" class="btn btn-primary">Iniciar sesión</button>
                          </form>
                        </div>
                        </div>
                    </div>
                  </form>

    </div>
  </div>
</div>



</div>

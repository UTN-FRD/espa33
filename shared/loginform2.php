<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  $temp_return_page = "";
  if (isset($_GET["RET"])){
    $_SESSION["returnPage"] = $_GET["RET"];
  }

  $tab = "home";
  $nav = "";
  $focus_form_name = "loginform";
  $focus_form_field = "username";
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,"shared");
?>

<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">

<form name="loginform" method="POST" action="../shared/login.php">
                    
                  <div class="row">
                        <div class="col-sm-3 col-sm-offset-4 form-box">
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
                              <button id="btnlogin" type="submit" class="btn btn-primary"><?php echo $loc->getText("loginFormLogin"); ?></button>
                          </form>
                        </div>
                        </div>
                    </div>
</form>

<?php include("../shared/footer.php"); ?>
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






<form name="loginform" method="POST" action="../shared/login.php">
                    
                  <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 form-box">
                          <div class="form-top">
                            <div class="form-top-left">
                                  <h3 style="color: #fff">Iniciar sesión</h3>
                            </div>
                            <div class="form-top-right">
                              <i class="fa fa-key"></i>
                            </div>
                            </div>
                            <div class="form-bottom">
                          <form role="form" action="" method="post" class="login-form">
                            <div class="form-group">
                              <label class="sr-only" for="form-username">Username</label>
                                <input type="user" name="username"  placeholder="Usuario" value="<?php if (isset($postVars["username"])) echo H($postVars["username"]); ?>" >

                                <font class="error"><?php if (isset($pageErrors["username"])) echo H($pageErrors["username"]); ?></font>

                              </div>
                              <div class="form-group">
                                <label class="sr-only" for="form-password">Password</label>
                                 <input type="password" placeholder="Contraseña" name="pwd"value="<?php if (isset($postVars["pwd"])) echo H($postVars["pwd"]); ?>" >

                                  <?php if (isset($pageErrors["pwd"])) echo H($pageErrors["pwd"]); ?></font>

                              </div>
                              <button id="btnlogin" type="submit" class="btn btn-primary"><?php echo $loc->getText("loginFormLogin"); ?></button>
                          </form>
                        </div>
                        </div>
                    </div>
</form>

<?php include("../shared/footer.php"); ?>




<!--
size="20" maxlength="20"
size="20" maxlength="20" 

<!--
<br>
<center>
<form name="loginform" method="POST" action="../shared/login.php">
<table class="primary">
  <tr>
    <th><?php echo $loc->getText("loginFormTbleHdr"); ?>:</th>
  </tr>
  <tr>
    <td valign="top" class="primary" align="left">
<table class="primary">
  <tr>
    <td valign="top" class="noborder">
      <?php echo $loc->getText("loginFormUsername"); ?>:</font>
    </td>
    <td valign="top" class="noborder">
      <input type="text" name="username" size="20" maxlength="20" value="<?php if (isset($postVars["username"])) echo H($postVars["username"]); ?>" >
      <font class="error"><?php if (isset($pageErrors["username"])) echo H($pageErrors["username"]); ?></font>
    </td>
  </tr>
  <tr>
    <td valign="top" class="noborder">
      <?php echo $loc->getText("loginFormPassword"); ?>:</font>
    </td>
    <td valign="top" class="noborder">
      <input type="password" name="pwd" size="20" maxlength="20" value="<?php if (isset($postVars["pwd"])) echo H($postVars["pwd"]); ?>" >
      <font class="error">
      <?php if (isset($pageErrors["pwd"])) echo H($pageErrors["pwd"]); ?></font>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center" class="noborder">
      <input type="submit" value="<?php echo $loc->getText("loginFormLogin"); ?>" class="button">
    </td>
  </tr>
</table>
    </td>
  </tr>
</table>

</form>
</center>
-->
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
  $nav = "view";
  $helpPage = "user";
  $focus_form_name = "loginform";
  $focus_form_field = "username";
 // require_once("../shared/get_form_vars.php");
//  $focus_form_name = "barcodesearch";
//  $focus_form_field = "searchText";
  // require_once("../shared/logincheck.php");
  require_once("../opac/header_opac.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
?>

<script type="text/javascript" ">
   function Validar(f) {
    if (f.searchText.value=='') {
      alert("Por favor Digite almenos Un digito del codigo / una letra del apellido ");
      f.searchText.focus();
      return (false);
    }
   }
</script>

<form name="loginform" method="POST" action="../user/login.php" onsubmit="return Validar(this);">

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
                              <label class="sr-only" for="form-username">Número de tarjeta</label>
                                <input class="form-control" type="user" name="barcode_nmbr" placeholder="Número de tarjeta" value="<?php if (isset($postVars["barcode_nmbr"])) echo H($postVars["barcode_nmbr"]); ?>" >

                                <font class="error"><?php if (isset($pageErrors["barcode_nmbr"])) echo H($pageErrors["barcode_nmbr"]); ?></font>

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
<!--
<table class="primary">
  <tr>
    <th><?php echo $loc->getText("indexCardHdr"); ?></th>
  </tr>
  <tr>
    <td valign="top" class="primary" align="left">
<table class="primary">
  <tr>
    <td valign="top" class="noborder">
      <?php echo $loc->getText("indexCard"); ?></font>
    </td>
    <td valign="top" class="noborder">
      <input type="text" name="barcode_nmbr" size="20" maxlength="20" value="<?php if (isset($postVars["barcode_nmbr"])) echo H($postVars["barcode_nmbr"]); ?>" >
      <font class="error"><?php if (isset($pageErrors["barcode_nmbr"])) echo H($pageErrors["barcode_nmbr"]); ?></font>
    </td>
  </tr>

  <tr>
    <td valign="top" class="noborder">
      <?php echo $loc->getText("loginFormPassword"); ?>:</font>
    </td>
    <td valign="top" class="noborder">
      <input type="password" name="pass_user" size="20" maxlength="20" value="<?php if (isset($postVars["pass_user"])) echo H($postVars["pass_user"]); ?>" > 
      <font class="error">
      <?php if (isset($pageErrors["pass_user"])) echo H($pageErrors["pass_user"]); ?></font>
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
-->

<?php include("../shared/footer.php"); ?>
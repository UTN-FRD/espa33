<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

   require_once("../shared/common.php");
  $tab = "user";
  $nav = "user_pass";
  $helpPage = "memberView";

  $focus_form_name = "pwdresetform";
  $focus_form_field = "pass_user";
 // $focus_form_name = "barcodesearch";
//  $focus_form_field = "barcodeNmbr";
  include("../user/logincheck.php");
  require_once("../opac/header_opac.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($_GET["UID"])){
    //unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);

    $postVars["barcode_nmbr"] = $_SESSION["barcode_nmbr"];
  } else {
    require("../shared/get_form_vars.php");
  }
  if (isset($pageErrors["pass_user2"])) echo "<div class='margin30 nomarginbottom alert alert-danger'>".H($pageErrors["pass_user2"])."</div>";
  if (isset($pageErrors["pass_user"])) echo "<div class='margin30 nomarginbottom alert alert-danger'>".H($pageErrors["pass_user"])."</div>";
?>
<div class="row">
  <div class="col-sm-5">
    <h3><?php echo $loc->getText("adminStaff_pwd_reset_form_Resetheader"); ?></h3>

    <form name="pass_userResetform" method="POST" action="../user/user_pwd_reset.php">

    <input type="hidden" name="barcode_nmbr" value="<?php echo H($postVars["barcode_nmbr"]);?>">
      <input class="form-control" name="pass_user" type="password" placeholder="Nueva contraseña" value="<?php if (isset($postVars["pass_user"])) echo H($postVars["pass_user"]); ?>" ><br>
        <div class="row">
        </div>
          <input class="form-control" type="password" name="pass_user2" placeholder="Repetir contraseña" value="<?php if (isset($postVars["pass_user2"])) echo H($postVars["pass_user2"]); ?>" ><br>
          <input type="submit" value="  <?php echo $loc->getText("adminSubmit"); ?>  " class="btn btn-primary">
      </form>

  </div>
</div>


</form>
<?php include("../shared/footer.php"); ?>
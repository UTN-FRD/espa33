<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
   require_once("../shared/common.php");
   
  $tab = "user";
  $nav = "account";
  
  $focus_form_name = "accttransform";
  $focus_form_field = "transactionTypeCd";

  require_once("../functions/inputFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../user/logincheck.php");
  require_once("../shared/get_form_vars.php");
  require_once("../classes/MemberAccountTransaction.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  /*if (count($_GET) == 0) {
    header("Location: ../user/index.php");
    exit();
  }
*/
  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $_SESSION["mbrid"];
  if (isset($_GET["msg"])) {
    $msg = "<font class=\"error\">".H($_GET["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  $mbrClassifyDm = $dmQ->getAssoc("transaction_type_dm");
  $dmQ->close();

  #****************************************************************************
  #*  Show transaction input form
  #****************************************************************************
  require_once("../opac/header_opac.php");
?>

<?php echo $msg ?>

<?php 
  #****************************************************************************
  #*  Search database for member account info
  #****************************************************************************
  $transQ = new MemberAccountQuery();
  $transQ->connect();
  if ($transQ->errorOccurred()) {
    $transQ->close();
    displayErrorPage($transQ);
  }
  if (!$transQ->doQuery($mbrid)) {
    $transQ->close();
    displayErrorPage($transQ);
  }
?>

<script type="text/javascript">
  document.getElementById("body").classList.add('userhome');
</script>

<link rel="stylesheet" href="../css/Material/material-kit.min.css">

 <div class="main main-raised" style="margin-top: 20px; background: #e8e8e8; min-height: 400px">
    <div class="profile-content">
        <div class="demo-layout-transparent mdl-layout mdl-js-layout">
          <div class="mdl-layout__drawer" style="border-radius: 6px">
          <span class="mdl-layout-title">Opciones</span>
          <nav class="mdl-navigation">
            <a class="mdl-navigation__link" href="../user/user_view.php?mbrid=<?php echo HURL($mbrid);?>">Información</a>
            <a class="mdl-navigation__link" href="../user/user_edit_form.php?mbrid=<?php echo HURL($mbrid);?>">Editar datos</a>
            <a class="mdl-navigation__link" style="background: #c5c5c5">Cuenta</a>
            <a class="mdl-navigation__link" href="../user/user_history.php?mbrid=<?php echo HURL($mbrid);?>">Historial</a>
          </nav>
          </div>
        </div>
        
        <div class="container">

          <div class="mdl-card mdl-shadow--2dp" style="width: 100%; margin-top: 50px; margin-bottom: 20px;">
            <div class="mdl-card__title">
              <h2 class="mdl-card__title-text" style="font-size: 32">
              <?php echo $loc->getText("mbrAccountHead1"); ?>
              </h2>
            </div>

          <table class="table nomargin nomarginbottom" style="margin: 0px 10px 10px 10px; width: 98%">
            <tr>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrAccountHdr2"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrAccountHdr3"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrAccountHdr4"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrAccountHdr5"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrAccountHdr6"); ?>
              </th>
              <!--<th valign="top" nowrap="yes" align="left">
                <?php //echo $loc->getText("mbrAccountHdr1"); ?>
              </th>-->
            </tr>

          <?php
            if ($transQ->getRowCount() == 0) {
          ?>
            <tr>
              <td class="primary" align="center" colspan="6">
                <?php echo $loc->getText("mbrAccountNoTrans"); ?>
              </td>
            </tr>
          <?php
            } else {
              $bal = 0;
              ?>
              <tr><td class="primary" colspan="5"><?php echo $loc->getText("mbrAccountOpenBal"); ?></td><td class="primary"><?php echo H(moneyFormat($bal,2));?></td></tr>

              <?php
              while ($trans = $transQ->fetchRow()) {
                $bal = $bal + $trans->getAmount();
          ?>
            <tr>
              <td class="primary" valign="top" >
                <?php echo date('d/m/y H:i',strtotime(($trans->getCreateDt())));?>
              </td>
              <td class="primary" valign="top" >
                <?php echo H($trans->getTransactionTypeDesc());?>
              </td>
              <td class="primary" valign="top" >
                <?php echo H($trans->getDescription());?>
              </td>
              <td class="primary" valign="top" >
                <?php echo H($trans->getAmount());?>
              </td>
              <td class="primary" valign="top" >
                <?php echo $bal;?>
              </td>
            </tr>
          <?php
              }
            }
            $transQ->close();

          ?>
          </table>

          </div>

        </div>
      </div>
    </div>

<?php require_once("../shared/footer.php"); ?>
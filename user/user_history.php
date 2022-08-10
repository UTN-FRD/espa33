<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  $tab = "user";
  $nav = "hist";

  require_once("../functions/inputFuncs.php");
  require_once("../user/logincheck.php");
  require_once("../classes/BiblioStatusHist.php");
  require_once("../classes/BiblioStatusHistQuery.php");
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

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  $mbrClassifyDm = $dmQ->getAssoc("mbr_classify_dm");
  $biblioStatusDm = $dmQ->getAssoc("biblio_status_dm");
  $materialTypeDm = $dmQ->getAssoc("material_type_dm");
  $materialImageFiles = $dmQ->getAssoc("material_type_dm", "image_file");
  $dmQ->close();

  #****************************************************************************
  #*  Search database for member history
  #****************************************************************************
  $histQ = new BiblioStatusHistQuery();
  $histQ->connect();
  if ($histQ->errorOccurred()) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  if (!$histQ->queryByMbrid($mbrid)) {
    $histQ->close();
    displayErrorPage($histQ);
  }

  #**************************************************************************
  #*  Show biblio checkout history
  #**************************************************************************
  require_once("../opac/header_opac.php");
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
            <a class="mdl-navigation__link" href="../user/user_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y">Cuenta</a>
            <a class="mdl-navigation__link" style="background: #c5c5c5">Historial</a>
          </nav>
          </div>
        </div>

        <div class="container">

          <div class="mdl-card mdl-shadow--2dp" style="width: 100%; margin-top: 50px; margin-bottom: 20px">
            <div class="mdl-card__title">
              <h2 class="mdl-card__title-text" style="font-size: 32">
              <?php echo $loc->getText("mbrHistoryHead1"); ?>
              </h2>
            </div>

          <table class="table nomargin nomarginbottom" style="margin: 0px 10px 10px 10px; width: 98%">
            <tr>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrHistoryHdr1"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrHistoryHdr2"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrHistoryHdr3"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrHistoryHdr4"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrHistoryHdr5"); ?>
              </th>
              <th valign="top" nowrap="yes" align="left">
                <?php echo $loc->getText("mbrHistoryHdr6"); ?>
              </th>
            </tr>

          <?php
            if ($histQ->getRowCount() == 0) {
          ?>
            <tr>
              <td class="primary" align="center" colspan="6">
                <?php echo $loc->getText("mbrHistoryNoHist"); ?>
              </td>
            </tr>
          <?php
            } else {
              while ($hist = $histQ->fetchRow()) {
          ?>
            <tr>
              <td class="primary" valign="top" >
                <?php echo H($hist->getBiblioBarcodeNmbr());?>
              </td>
              <td class="primary" valign="top" >
                <a href="../shared/biblio_view.php?bibid=<?php echo HURL($hist->getBibid());?>&amp;tab=opac"><?php echo H($hist->getTitle());?></a>
              </td>
              <td class="primary" valign="top" >
                <?php echo H($hist->getAuthor());?>
              </td>
              <td class="primary" valign="top" >
                <?php echo H($biblioStatusDm[$hist->getStatusCd()]);?>
              </td>
              <td class="primary" valign="top" >
                <?php echo date('d/m/y H:i',strtotime(($hist->getStatusBeginDt())));?>
              </td>
              <td class="primary" valign="top" >
                <?php if ($hist->getDueBackDt() != '') echo date('d/m/y',strtotime(($hist->getDueBackDt())));?>
              </td>
            </tr>
          <?php
              }
            }
            $histQ->close();

          ?>
          </table>
          </div>

        </div>
      </div>
    </div>


<?php require_once("../shared/footer.php"); ?>
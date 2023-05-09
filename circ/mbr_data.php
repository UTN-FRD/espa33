<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "circulation";
$nav = "mdata";
$helpPage = "memberView";
$focus_form_name = "barcodesearch";
$focus_form_field = "barcodeNmbr";

require_once("../shared/logincheck.php");
require_once("../classes/Member.php");
require_once("../classes/MemberQuery.php");
require_once("../classes/DmQuery.php");
require_once("../classes/Localize.php");
$loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_GET) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $_GET["mbrid"];
  if (isset($_GET["msg"])) {
    $msg = "<div class='margin30 nomarginbottom alert alert-info'>".H($_GET["msg"])."</div>";
  } else {
    $msg = "";
  }

  #****************************************************************************
  #*  Search database for member
  #****************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->connect();
  $mbr = $mbrQ->get($mbrid);
  $mbrQ->close();

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  $mbrClassifyDm = $dmQ->getAssoc("mbr_classify_dm");
  $dmQ->close();

  #**************************************************************************
  #*  Show member information
  #**************************************************************************
  require_once("../shared/header.php");

  ?>
  <!--****************************************************************************
  *  Panel de informacion de socio
  **************************************************************************** -->

<div class="row">
<div class="col-sm-8">


<div class="row user-infos cyruxx">
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-1 col-lg-offset-1">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Información de socio</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3 hidden-xs hidden-sm">
                    <?php if  (H($mbr->getFoto())) { ?>
                        <div style="background-image: url( <?php echo  ".." . FOTO_PATH ."/" . H($mbr->getFoto());?>)"  class="circle-avatar"></div>
                    <?php } else { ?>
                        <div style="background-image: url(../images/avatar_2x-100.png)" class="circle-avatar"></div>
                    <?php  } ?> 
                    </div>
                    <div class=" col-md-9 col-lg-9 hidden-xs hidden-sm">
                        <table class="nomargin table table-user-information">
                            <tbody>
                            <tr>
                                <td>
                                Tarjeta
                                </td>
                                <td>
                                <?php echo H($mbr->getBarcodeNmbr());?>
                                </td>
                            </tr>
                            <tr>
                            <td nowrap="true" class="primary" valign="top">
                                DNI
                            </td>
                            <td valign="top" class="primary">
                                <?php echo H($mbr->getDni());?>
                            </td>
                            </tr>
                            <tr>
                            <td nowrap="true" class="primary" valign="top">
                                Legajo
                            </td>
                            <td valign="top" class="primary">
                                <?php echo H($mbr->getLegajo());?>
                            </td>
                            </tr>
                            <tr>
                                <td>
                                <?php echo $loc->getText("mbrViewClassify"); ?>
                                </td>
                                <td>
                                <?php echo H($mbrClassifyDm[$mbr->getClassification()]);?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <?php echo $loc->getText("mbrViewAddr"); ?>
                                </td>
                                <td>
                                <?php
                                    echo str_replace("\n", "<br />", H($mbr->getAddress()));
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <?php echo $loc->getText("mbrViewStatus"); ?>
                                </td>
                                <td>
                                <?php
                                    if (strcmp($mbr->getStatus(), "Y") == 0) {
                                    echo $loc->getText("mbrActive"); 
                                    } elseif (strcmp($mbr->getStatus(), "N") == 0) {
                                    echo $loc->getText("mbrInactive"); 
                                    }
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <?php echo $loc->getText("mbrViewPhone"); ?>
                                </td>
                                <td>
                                <?php
                                    if ($mbr->getHomePhone() != "") {
                                    echo $loc->getText("mbrViewPhoneHome").' '.$mbr->getHomePhone()."</br> ";
                                    }
                                    if ($mbr->getWorkPhone() != "") {
                                    echo $loc->getText("mbrViewPhoneWork").' '.$mbr->getWorkPhone()."</br> ";
                                    }
                                    if ($mbr->getCel() != "") {
                                    echo $loc->getText("mbrViewCel").' '.$mbr->getCel();
                                    }

                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <?php echo $loc->getText("mbrViewEmail"); ?>
                                </td>
                                <td>
                                <?php echo H($mbr->getEmail());?>
                                </td>
                            </tr>
                            <tr>
                                <td class="primary" valign="top">
                                <?php echo $loc->getText("mbrViewBornDt"); ?>
                                </td>
                                <td valign="top" class="primary">
                                <?php echo H($mbr->getBornDt());?>
                            </td>
                            </tr>
                            <tr>
                                <td class="primary" valign="top">
                                <?php echo $loc->getText("mbrViewLastActDate"); ?>
                                </td>
                                <td valign="top" class="primary">
                                <?php echo H($mbr->getLastActDate());?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel-footer" id="user-panel-footer">
                <!--<button class="btn btn-sm btn-primary" type="button"
                        data-toggle="tooltip"
                        data-original-title="Send message to user"><i class="glyphicon glyphicon-envelope"></i></button>-->
                <span class="pull-right">
                    <a class="btn btn-primary" href="javascript:popSecondary('../circ/mbr_print_carnet.php?mbrid=<?php echo H(addslashes(U($mbrid)));?>')"><?php echo $loc->getText("mbrPrintcarnet"); ?></a>
                    <a class="btn btn-primary"  href="../circ/mbr_pwd_reset_form.php?UID=<?php  echo HURL( H($mbr->getBarcodeNmbr()));?>&mbrid=<?php echo $mbrid;?>" class="<?php //echo H($row_class);?>"><?php  echo $loc->getText("Reset pass"); ?></a>
                </span>
            </div>
        </div>
    </div>
</div>

<?php require_once("../shared/footer.php"); ?>
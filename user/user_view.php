<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  $tab = "user";
  $nav = "user_view";
  $helpPage = "memberView";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcodeNmbr";

  require_once("../functions/inputFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../user/logincheck.php");
  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../classes/BiblioSearch.php");
  require_once("../classes/BiblioSearchQuery.php");
  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../classes/DmQuery.php");
  require_once("../shared/get_form_vars.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
 if (count($_GET) == 0 && count($_POST) == 0 && count($_SESSION) == 0 ) {
    header("Location: ../user/user_view.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid =$_SESSION["mbrid"];
  if (isset($_SESSION["msg"])) {
    $msg = "<font class=\"error\">".H($_SESSION["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  $mbrClassifyDm = $dmQ->getAssoc("mbr_classify_dm");
  $mbrMaxFines = $dmQ->getAssoc("mbr_classify_dm", "max_fines");
  $biblioStatusDm = $dmQ->getAssoc("biblio_status_dm");
  $materialTypeDm = $dmQ->getAssoc("material_type_dm");
  $materialImageFiles = $dmQ->getAssoc("material_type_dm", "image_file");
  $memberFieldsDm = $dmQ->getAssoc("member_fields_dm");
  $dmQ->close();

  #****************************************************************************
  #*  Search database for member
  #****************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->connect();
  $mbr = $mbrQ->get($mbrid);
  $mbrQ->close();

  #****************************************************************************
  #*  Check for outstanding balance due
  #****************************************************************************
  $acctQ = new MemberAccountQuery();
  $acctQ->connect();
  if ($acctQ->errorOccurred()) {
    $acctQ->close();
    displayErrorPage($acctQ);
  }
  $balance = $acctQ->getBalance($mbrid);
  if ($acctQ->errorOccurred()) {
    $acctQ->close();
    displayErrorPage($acctQ);
  }
  $acctQ->close();
  
  #**************************************************************************
  #*  Show member information
  #**************************************************************************
  require_once("../opac/header_opac.php");
  $balMsg = "";
  if ($balance > 0 && $balance >= $mbrMaxFines[$mbr->getClassification()]) {
    $balText = moneyFormat($balance, 2);
    $balMsg = "<font class=\"error\">".$loc->getText("mbrViewBalMsg",array("bal"=>$balText))."</font><br><br>";
  }
?>

<?php echo $balMsg ?>
<?php echo $msg;
?>

<div class="row">
  <div class="col-sm-5">
      <div class="margin30 panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo $loc->getText("mbrViewHead1"); ?></h3>
        </div>
        <div class="nopadding panel-body">

<table class="nomarginbottom nomargin table">
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("mbrViewName"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbr->getFirstName());?>
    </td>
  </tr>

  <tr>
    <td nowrap="true" class="primary" valign="top">
      Apellido:
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbr->getLastName());?>
    </td>
  </tr>

  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewAddr"); ?>
    </td>
    <td valign="top" class="primary">
      <?php
        echo str_replace("\n", "<br />", H($mbr->getAddress()));
      ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewCardNmbr"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbr->getBarcodeNmbr());?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewClassify"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbrClassifyDm[$mbr->getClassification()]);?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewStatus"); ?>
    </td>
    <td valign="top" class="primary">
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
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewPhone"); ?>
    </td>
    <td valign="top" class="primary">
      <?php
        if ($mbr->getHomePhone() != "") {
          echo $loc->getText("mbrViewPhoneHome").$mbr->getHomePhone()."</br> ";
        }
        if ($mbr->getWorkPhone() != "") {
          echo $loc->getText("mbrViewPhoneWork").$mbr->getWorkPhone()."</br> ";
        }
        if ($mbr->getCel() != "") {
          echo $loc->getText("mbrViewCel").$mbr->getCel();
        }

      ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewEmail"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbr->getEmail());?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewPassUser"); ?>
    </td>
    <td valign="top" class="primary">
      <?php // echo H($mbr->getPassUser());?>
      <?php echo "****";?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewBornDt"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbr->getBornDt());?>
      <?php echo "</br>" . $loc->getText("mbrFormattedDateOld", array('date' => $mbr->getBornDt()));?>
  </td>
  </tr>


  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewOther"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbr->getOther());?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewLastActDate"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $loc->getText("mbrFormattedDate", array('date' => $mbr->getLastActDate()));?>
     <?php echo "</br>" . H($mbr->getLastActDate());?>
    </td>
  </tr>

<?php
  foreach ($memberFieldsDm as $name => $title) {
    if ($value = $mbr->getCustom($name)) {
?>
  <tr>
    <td class="primary" valign="top">
      <?php echo H($title); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($value);?>
    </td>
  </tr>
<?php
    }
  }
?>
     <?php // Modificado para mostrar foto usuario ?>
   <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("mbrViewFotHdr1"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($mbr->getFoto());?>
    </td>
  </tr>

</table>

  </div></div></div>

  <div class="col-sm-5">
      <div class="margin30 panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Historial de prestamos</h3>
        </div>

        <div class="nopadding panel-body">


<?php
  #****************************************************************************
  #*  Show checkout stats
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  $dms = $dmQ->getCheckoutStats($mbr->getMbrid());
  $dmQ->close();
?>
<table class="nomarginbottom nomargin table">
  <tr>
    <th align="left" rowspan="2">
      <?php echo $loc->getText("mbrViewStatColHdr1"); ?>
    </th>
    <th align="left" rowspan="2">
      <?php echo $loc->getText("mbrViewStatColHdr2"); ?>
    </th>
    <th align="center" colspan="2" nowrap="yes">
      <?php echo $loc->getText("mbrViewStatColHdr3"); ?>
    </th>
  </tr>
  <tr>
    <th align="left">
      <?php echo $loc->getText("mbrViewStatColHdr4"); ?>
    </th>
    <th align="left">
      <?php echo $loc->getText("mbrViewStatColHdr5"); ?>
    </th>
  </tr>
<?php
  foreach ($dms as $dm) {
?>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo H($dm->getDescription()); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($dm->getCount()); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($dm->getCheckoutLimit()); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo H($dm->getRenewalLimit()); ?>
    </td>
  </tr>
<?php
  }
?>
  </table>
</div></div>

<!--****************************************************************************
    *  Muestra imagen de usuario 
    *     Show imagen user
    *    Modificado Jose,  Lara joanlaga@hotmail.com
	*     <?php // Modificado para mostrar foto usuario ?>
    **************************************************************************** -->
     <?php if  (H($mbr->getFoto())) { ?>
<table class="table">
   <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHead8"); ?>
     </th>
   </tr>
   <tr>   
     <td valign="top" class="primary">
       <img src="<?php echo  "../" . FOTO_PATH ."/" . H($mbr->getFoto());?>" width="250">
     </td>
   </tr>
 </table>

<?php  } ?>


     <?php // Modificado para mostrar foto usuario ?>


<!--****************************************************************************
    *  Imprime carnet y Reset pwd
    **************************************************************************** -->

<div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Administración</h3>
          </div>
          <div class="panel-body">
            <a class="btn btn-primary" href="javascript:popSecondary('../user/user_print_carnet.php?mbrid=<?php echo H(addslashes(U($mbrid)));?>')"><?php echo $loc->getText("mbrPrintcarnet"); ?></a>
            <a class="btn btn-primary"  href="../user/user_pwd_reset_form.php?UID=<?php  echo HURL( H($mbr->getBarcodeNmbr()));?>" class="<?php   echo H($row_class);?>"><?php  echo $loc->getText("Reset pass"); ?></a>
          </div>
        </div>
        </div>
        </div>


<!--****************************************************************************
    *  Checkout form
    **************************************************************************** -->
<form name="barcodesearch" method="POST" action="../user/checkout.php">
<input type="hidden" name="mbrid" value="<?php echo H($mbrid);?>">
<input type="hidden" name="date_from" id="date_from" value="default" />
<script type="text/javascript">
function showDueDate() {
  el = document.getElementById('date_from');
  el.value = "override";
  el = document.getElementById('duedateoverride');
  el.style.display = "none";
  el = document.getElementById('duedate1');
  el.style.display = "inline";
  el = document.getElementById('duedate2');
  el.style.display = "inline";
  el = document.getElementById('duedate3');
  el.style.display = "inline";
}
function hideDueDate() {
  el = document.getElementById('date_from');
  el.value = "default";
  el = document.getElementById('duedateoverride');
  el.style.display = "inline";
  el = document.getElementById('duedate1');
  el.style.display = "none";
  el = document.getElementById('duedate2');
  el.style.display = "none";
  el = document.getElementById('duedate3');
  el.style.display = "none";
}
</script>

<!-- 
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHead3"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <table class="primary">
      <tr>
      <td class="borderless"><?php echo $loc->getText("mbrViewBarcode"); ?></td>
      <td class="borderless">
        <?php printInputText("barcodeNmbr",18,18,$postVars,$pageErrors); ?>
        <a href="javascript:popSecondaryLarge('../opac/index.php?lookup=Y')"><?php echo $loc->getText("indexSearch"); ?></a>
      </td><td class="borderless">
        <input type="submit" value="<?php echo $loc->getText("mbrViewCheckOut"); ?>" class="button">
      </td>
      </tr>
      </table>      
    </td>
  </tr>
</table>
 -->

<?php if (isset($_SESSION['postVars']['date_from']) && $_SESSION['postVars']['date_from'] == 'override') { ?>
<script type="text/javascript">showDueDate()</script>
<?php } ?>
</form>

<!--<h1><?php echo $loc->getText("mbrViewHead4"); ?>
</h1>
-->
<div class="margin30 panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Material actualmente prestado 
</h3>
  </div>
  <div class="nopadding panel-body">
 
<table class="nomarginbottom nomargin table">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewOutHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewOutHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewOutHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewOutHdr4"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewOutHdr5"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewOutHdr6"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr8"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr7"); ?>
    </th>
     <th valign="top" align="left">
       <?php echo $loc->getText("mbrViewOutHdr10"); ?>
     </th>
  </tr>

<?php
  #****************************************************************************
  #*  Search database for BiblioStatus data
  #****************************************************************************
  $biblioQ = new BiblioSearchQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->doQuery(OBIB_STATUS_OUT,$mbrid)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if ($biblioQ->getRowCount() == 0) {
?>
  <tr>
    <td class="primary" align="center" colspan="9">
      <?php echo $loc->getText("mbrViewNoCheckouts"); ?>
    </td>
  </tr>
<?php
  } else {
    while ($biblio = $biblioQ->fetchRow()) {
?>
  <tr<?php echo $biblio->getDaysLate() > 0 ? ' class="biblio-late"' : ''; ?>>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo H($biblio->getStatusBeginDt());?>
    </td>
    <td class="primary" valign="top">
      <img src="../images/<?php echo HURL($materialImageFiles[$biblio->getMaterialCd()]);?>" width="20" height="20" border="0" align="middle" alt="<?php echo H($materialTypeDm[$biblio->getMaterialCd()]);?>">
      <?php echo H($materialTypeDm[$biblio->getMaterialCd()]);?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getBarcodeNmbr());?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo HURL($biblio->getBibid());?>&tab=opac"><?php echo H($biblio->getTitle());?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getAuthor());?>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo H($biblio->getDueBackDt());?>
    </td>
    <td class="primary" valign="top" >
      <a href="../user/checkout.php?barcodeNmbr=<?php echo HURL($biblio->getBarcodeNmbr());?>&amp;mbrid=<?php echo HURL($mbrid);?>&amp;renewal">Renew item</A>
      <?php
        if($biblio->getRenewalCount() > 0) { ?>
          </br>
          (<?php echo H($biblio->getRenewalCount());?> <?php echo $loc->getText("mbrViewOutHdr9"); ?>)
      <?php
        } ?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getDaysLate());?>
    </td>
     <td class="primary" valign="top" >
       <a href="../user/shelving_cart.php?barcodeNmbr=<?php echo HURL($biblio->getBarcodeNmbr());?>"><?php echo $loc->getText("To Shelving Cart"); ?></A>
     </td>
  </tr>
<?php
    }
  }
  $biblioQ->close();
?>
</table>
<br>
<font class="primary"> 
  <a class="btn btn-default" id="ImprimirSalidas" href="javascript:popSecondary('../user/user_print_checkouts.php?mbrid=<?php echo H(addslashes(U($mbrid)));?>')"><?php echo $loc->getText("mbrPrintCheckouts"); ?></a>

<!--   
  <a href="../user/user_renew_all.php?mbrid=<?php echo HURL($mbrid); ?>"><?php echo $loc->getText("Renew All"); ?></a>
   -->
</font>
</div>
</div>


<!--
<?php echo H($mbrid);?>
-->

<div class="margin30 panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Material actualmente en reserva 
</h3>
  </div>
  <div class="nopadding panel-body">
<table class="nomarginbottom nomargin table">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr4"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr5"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr6"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr7"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr8"); ?>
    </th>
  </tr>
<?php
  #****************************************************************************
  #*  Search database for BiblioHold data
  #****************************************************************************
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  if (!$holdQ->queryByMbrid($mbrid)) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  if ($holdQ->getRowCount() == 0) {
?>
  <tr>
    <td class="primary" align="center" colspan="8">
      <?php echo $loc->getText("mbrViewNoHolds"); ?>
    </td>
  </tr>
<?php
  } else {
    while ($hold = $holdQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" nowrap="yes">
      <a href="../shared/hold_del_confirm.php?bibid=<?php echo HURL($hold->getBibid());?>&amp;copyid=<?php echo HURL($hold->getCopyid());?>&amp;holdid=<?php echo HURL($hold->getHoldid());?>&amp;mbrid=<?php echo HURL($mbrid);?>"><?php echo $loc->getText("mbrViewDel"); ?></a>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo H($hold->getHoldBeginDt());?>
    </td>
    <td class="primary" valign="top">
      <img src="../images/<?php echo HURL($materialImageFiles[$hold->getMaterialCd()]);?>" width="20" height="20" border="0" align="middle" alt="<?php echo H($materialTypeDm[$hold->getMaterialCd()]);?>">
      <?php echo H($materialTypeDm[$hold->getMaterialCd()]);?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($hold->getBarcodeNmbr());?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo HURL($hold->getBibid());?>"><?php echo H($hold->getTitle());?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($hold->getAuthor());?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblioStatusDm[$hold->getStatusCd()]);?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($hold->getDueBackDt());?>
    </td>
  </tr>
<?php
    }
  }
  $holdQ->close();
?>
</table>
</div>
</div>


<?php require_once("../shared/footer.php"); ?>
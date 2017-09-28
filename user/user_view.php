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
    $msg = "<div class='margin30 nomarginbottom alert alert-info'>".H($_SESSION["msg"])."</div>";
  } else {
    $msg = "";
  }
  if (isset($_GET["msg"])) {
    $msg = "<div class='margin30 nomarginbottom alert alert-info'>".H($_GET["msg"])."</div>";
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
    $balMsg = "<div class='margin30 nomarginbottom alert alert-danger'>".$loc->getText("mbrViewBalMsg",array("bal"=>$balText))."</div>";
  }
?>

<?php echo $balMsg ?>
<?php echo $msg;
?>

<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    var panels = $('.user-infos');
    var panelsButton = $('.dropdown-user');
    panels.hide();

    //Click dropdown
    panelsButton.click(function() {
        //get data-for attribute
        var dataFor = $(this).attr('data-for');
        var idFor = $(dataFor);

        //current button
        var currentButton = $(this);
        idFor.slideToggle(400, function() {
            //Completed slidetoggle
            if(idFor.is(':visible'))
            {
                currentButton.html('<i class="glyphicon glyphicon-chevron-up text-muted"></i>');
            }
            else
            {
                currentButton.html('<i class="glyphicon glyphicon-chevron-down text-muted"></i>');
            }
        })
    });


    $('[data-toggle="tooltip"]').tooltip();

    $('button').click(function(e) {
        e.preventDefault();
    });
});
</script>

<div class="row">
  <div class="col-sm-7">


      <div class="borderwell well">
        <div class="row user-row">
            <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
                <?php if  (H($mbr->getFoto())) { ?>
                  <div style="background-image: url( <?php echo  ".." . FOTO_PATH ."/" . H($mbr->getFoto());?>)" class="noresponsive circle-avatar"></div>
                <?php } else { ?>
                  <div style="background-image: url(../images/avatar_2x-50.png)" class="noresponsive circle-avatar"></div>
                <?php  } ?>
            </div>
            <div class="col-xs-8 col-sm-9 col-md-10 col-lg-10" style="margin-top: 5px">
                <w style="font-family: Roboto; font-size: 25px; margin-left: 20px;"><?php echo " "; echo H($mbr->getFirstName());?> <?php echo H($mbr->getLastName());?>
                </w>
            </div>
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 dropdown-user" data-for=".cyruxx">
                <i class="glyphicon glyphicon-chevron-down text-muted"></i>
            </div>
        </div>
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
                                          <?php echo $loc->getText("mbrViewClassify"); ?>
                                        </td>
                                        <td>
                                          <?php echo H($mbrClassifyDm[$mbr->getClassification()]);?>
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
                                        <td>
                                          <?php echo $loc->getText("mbrViewEmail"); ?>
                                        </td>
                                        <td>
                                          <?php echo H($mbr->getEmail());?>
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
                            <a class="btn btn-primary" href="javascript:popSecondary('../user/user_print_carnet.php?mbrid=<?php echo H(addslashes(U($mbrid)));?>')"><?php echo $loc->getText("mbrPrintcarnet"); ?></a>
                            <a class="btn btn-primary"  href="../user/user_pwd_reset_form.php?UID=<?php  echo HURL( H($mbr->getBarcodeNmbr()));?>" class="<?php   echo H($row_class);?>"><?php  echo $loc->getText("Reset pass"); ?></a>
                        </span>
                    </div>
                </div>
            </div>
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


<?php if (isset($_SESSION['postVars']['date_from']) && $_SESSION['postVars']['date_from'] == 'override') { ?>
<script type="text/javascript">showDueDate()</script>
<?php } ?>
</form>

<!--<h1><?php echo $loc->getText("mbrViewHead4"); ?>
</h1>
-->

    <h3>Material actualmente prestado</h3>
    <hr style="margin-bottom: 15">
 
<table class="nomarginbottom nomargin table">
  <tr>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr1"); ?>
    </th>
    <!--<th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewOutHdr2"); ?>
    </th>-->
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr3"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr4"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr5"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr6"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr8"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr7"); ?>
    </th>
     <th valign="top" align="left">
    <!--   <?php echo $loc->getText("mbrViewOutHdr10"); ?>
     </th> -->
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
      <?php echo date('d/m/y H:i',strtotime($biblio->getStatusBeginDt()));?>
    </td>
    <!--<td class="primary" valign="top">
      <img src="../images/<?php echo HURL($materialImageFiles[$biblio->getMaterialCd()]);?>" width="20" height="20" border="0" align="middle" alt="<?php echo H($materialTypeDm[$biblio->getMaterialCd()]);?>">
      <?php echo H($materialTypeDm[$biblio->getMaterialCd()]);?>
    </td>-->
    <td class="primary" valign="top" >
      <?php echo H($biblio->getBarcodeNmbr());?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo HURL($biblio->getBibid());?>&tab=opac"><?php echo H($biblio->getTitle());?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getAuthor());?>
    </td>
    <td class="primary" valign="top">
      <?php echo date('d/m/y',strtotime($biblio->getDueBackDt()));?>
    </td>
    <td class="primary" valign="top" >
      <!--<a href="../user/checkout.php?barcodeNmbr=<?php echo HURL($biblio->getBarcodeNmbr());?>&amp;mbrid=<?php echo HURL($mbrid);?>&amp;renewal">Renew item</A>-->
      <?php
        if($biblio->getRenewalCount() > 0) { ?>
          
          <?php echo H($biblio->getRenewalCount()/24);?> <?php echo $loc->getText("mbrViewOutHdr9"); ?>
      <?php
        } ?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getDaysLate());?> Día/s
    </td>
    <!-- <td class="primary" valign="top" >
       <a href="../user/shelving_cart.php?barcodeNmbr=<?php echo HURL($biblio->getBarcodeNmbr());?>"><?php echo $loc->getText("To Shelving Cart"); ?></A>
     </td> -->
  </tr>
<?php
    }
  }
  $biblioQ->close();
?>
</table>
<br><!--
<font class="primary"> 
  <a class="btn btn-default" id="ImprimirSalidas" href="javascript:popSecondary('../user/user_print_checkouts.php?mbrid=<?php echo H(addslashes(U($mbrid)));?>')"><?php echo $loc->getText("mbrPrintCheckouts"); ?></a>
-->
<!--   
  <a href="../user/user_renew_all.php?mbrid=<?php echo HURL($mbrid); ?>"><?php echo $loc->getText("Renew All"); ?></a>
   -->
</font>


<!--
<?php echo H($mbrid);?>
-->


    <h3>Material actualmente en reserva</h3>
    <hr style="margin-bottom: 15">

<table class="nomarginbottom nomargin table">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr2"); ?>
    </th>
    <!--<th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("mbrViewHoldHdr3"); ?>
    </th>-->
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
    <th valign="top" nowrap="yes" align="left">
      <?php //echo $loc->getText("mbrViewHoldHdr1"); ?>
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
      <?php echo date('d/m/y H:i',strtotime($hold->getHoldBeginDt()));?>
    </td>
    <!--<td class="primary" valign="top">
      <img src="../images/<?php echo HURL($materialImageFiles[$hold->getMaterialCd()]);?>" width="20" height="20" border="0" align="middle" alt="<?php echo H($materialTypeDm[$hold->getMaterialCd()]);?>">
      <?php echo H($materialTypeDm[$hold->getMaterialCd()]);?>
    </td>-->
    <td class="primary" valign="top" >
      <?php echo H($hold->getBarcodeNmbr());?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo HURL($hold->getBibid());?>&tab=opac"><?php echo H($hold->getTitle());?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($hold->getAuthor());?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblioStatusDm[$hold->getStatusCd()]);?>
    </td>
    <td class="primary" valign="top" >
      <?php if ($hold->getDueBackDt() != '') echo date('d/m/y',strtotime($hold->getDueBackDt()));?>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <a class="glyphicon glyphicon-trash" href="../shared/hold_del_confirm.php?bibid=<?php echo HURL($hold->getBibid());?>&amp;copyid=<?php echo HURL($hold->getCopyid());?>&amp;holdid=<?php echo HURL($hold->getHoldid());?>&amp;mbrid=<?php echo HURL($mbrid);?>&mode=1"><?php //echo $loc->getText("mbrViewDel"); ?></a>
    </td>
  </tr>
<?php
    }
  }
  $holdQ->close();
?>
</table>


<?php require_once("../shared/footer.php"); ?>
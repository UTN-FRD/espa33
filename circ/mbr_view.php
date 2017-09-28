<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "view";
  $helpPage = "memberView";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcodeNmbr";

  require_once("../functions/inputFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../shared/logincheck.php");
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
  require_once("../shared/header.php");
  $balMsg = "";
  if ($balance > 0 && $balance >= $mbrMaxFines[$mbr->getClassification()]) {
    $balText = moneyFormat($balance, 2);
    $balMsg = "<div class='margin30 nomarginbottom alert alert-danger'>".$loc->getText("mbrViewBalMsg",array("bal"=>$balText))."</div>";
  }
?>

<?php echo $balMsg ?>
<?php echo $msg ?>

<!--<table class="table">
  <tr><td class="noborder" valign="top">
  <br>-->

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

<div class="container-fluid">


<form style="margin-bottom: 0px;" name="barcodesearch" method="POST" action="../circ/checkout.php">

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
  el.style.display = "";
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

<!--****************************************************************************
    *  Prestar                                                                 *
    **************************************************************************** -->



    <h3>Prestar material</h3>
    <hr style="margin-bottom: 15px;">



  <div class="row">
    <div class="col-md-7">
          <div class="col-lg-3" style="min-width: 185px;">                  
            <select class="form-control" name="searchType">
              <option value="rfid" selected>Código RFID
              <option value="barcode">Número de copia</option>
            </select>             
          </div>
          <div class="input-group">
            <?php printInputText("barcodeNmbr",18,30,$postVars,$pageErrors); ?>
            <?php //printInputText("rfid",18,30,$postVars,$pageErrors); ?>
            <span class="bottomalign input-group-btn">
              <input id="submit" type="submit" value="<?php echo $loc->getText("mbrViewCheckOut"); ?>" class="btn btn-primary">
            </span>
          </div>

          <span id="duedate1" style="display:none">
          <!--<?php echo $loc->getText("Due Date:"); ?>-->
          </span>

    </div>

    <script type="text/javascript">
      $('#barcodeNmbr').attr('placeholder','Número');

      $('#barcodeNmbr').keyup(function () {
        if (this.value.length == 25) {
            $('#submit').click();
        }
      });

      $(window).on('load',function(){
        $('#myModal').modal('show');
      });

      $('#close').on("click", function(){
        $('#barcodeNmbr').val('');
        $('#barcodeNmbr').focus();
      });
    </script>

    <!--<div class="nowidth col-sm-3">
          <a class="btn btn-primary" href="javascript:popSecondary('../circ/mbr_print_checkouts.php?mbrid=<?php echo H(addslashes(U($mbrid)));?>')"><?php echo $loc->getText("mbrPrintCheckouts"); ?></a>
          <a class="btn btn-primary" href="../circ/mbr_renew_all.php?mbrid=<?php echo HURL($mbrid); ?>"><?php echo $loc->getText("Renew All"); ?></a>
            
    </div>-->
<script type="text/javascript">
  $(document).ready(function() {
   // On refresh check if there are values selected
    if (localStorage.selectVal) {
    // Select the value stored
    $('select').val( localStorage.selectVal );
    }
  });

  // On change store the value
  $('select').on('change', function(){
    var currentVal = $(this).val();
    localStorage.setItem('selectVal', currentVal );
  });
</script>

    <div class="col-sm-3">

          <small id="duedateoverride"><a class="btn btn-default" href="javascript:showDueDate()"><?php echo $loc->getText("Override Due Date"); ?></a></small>
          
          <span id="duedate2" style="display:none">
            <div class="input-group">
                <?php 
                  if (isset($_SESSION['due_date_override']) && !isset($postVars['dueDate'])) {
                    $postVars['dueDate'] = $_SESSION['due_date_override'];
                  }
                  printInputText("dueDate",18,18,$postVars,$pageErrors);
                ?>
              <span class="input-group-btn" id="duedate3" style="display:none">
                <input type="button" value="<?php echo $loc->getText("Cancel"); ?>" class="btn btn-default" onclick="hideDueDate()" />
                </input>
              </span>
            </div>
          </span>
    </div>   
  </div>

  <script>
    $('#dueDate').attr('placeholder','DD.MM.AAAA');
  </script>

<?php if (isset($_SESSION['postVars']['date_from']) && $_SESSION['postVars']['date_from'] == 'override') { ?>
<script type="text/javascript">showDueDate()</script>
<?php } ?>
</form>

<!--<h1><?php echo $loc->getText("mbrViewHead4"); ?>-->
 

</h1>

<div class="row">
<table class="insidepanel nomarginbottom table">
  <tr>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr1"); ?>
    </th>
   <!-- <th valign="top" nowrap="yes" align="left">
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
      <?php echo $loc->getText("mbrViewOutHdr7"); ?>
    </th>
    <th valign="top" align="left">
      <?php //echo $loc->getText("mbrViewOutHdr8"); ?>
    </th>
     <th valign="top" align="left">
       <?php //echo $loc->getText("mbrViewOutHdr10"); ?>
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
      <?php echo date('d/m/y H:i',strtotime($biblio->getStatusBeginDt()));?>
    </td>
   <!-- <td class="primary" valign="top">
      <img src="../images/<?php echo HURL($materialImageFiles[$biblio->getMaterialCd()]);?>" width="20" height="20" border="0" align="middle" alt="<?php echo H($materialTypeDm[$biblio->getMaterialCd()]);?>">
      <?php echo H($materialTypeDm[$biblio->getMaterialCd()]);?>
    </td>-->
    <td class="primary" valign="top" >
      <?php echo H($biblio->getBarcodeNmbr());?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo HURL($biblio->getBibid());?>"><?php echo H($biblio->getTitle());?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getAuthor());?>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo date('d/m/y',strtotime(($biblio->getDueBackDt())));?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getDaysLate());?> Día/s
    </td>
    <td nowrap class="primary" valign="top" >
      <a data-toggle="tooltip" title="Renovar" href="../circ/checkout.php?barcodeNmbr=<?php echo HURL($biblio->getBarcodeNmbr());?>&amp;mbrid=<?php echo HURL($mbrid);?>&amp;renewal" class="glyphicon glyphicon-refresh"></a>
      <?php
        if($biblio->getRenewalCount() > 0) { ?>
          <?php echo H($biblio->getRenewalCount()/24);?> <?php echo $loc->getText("mbrViewOutHdr9"); ?>
      <?php
        } ?>
    </td>
     <td class="primary" valign="top" >
       <a data-toggle="tooltip" title="Devolver" href="../circ/shelving_cart.php?barcodeNmbr=<?php echo HURL($biblio->getBarcodeNmbr());?>" class="glyphicon glyphicon-inbox"></a>
     </td>
  </tr>
<?php
    }
  }
  $biblioQ->close();
?>

</table>

</div>

<br>
<!--****************************************************************************
    *  Hold form - Reservar
    **************************************************************************** -->


    <h3>Reservar material</h3>
    <hr style="margin-bottom: 15px;">

<div class="row">
  <div class="row">
    <div class="col-sm-12">

      <form name="holdForm" method="POST" action="../circ/place_hold.php">

      <!--
            <?php echo $loc->getText("mbrViewHead5"); ?>

            <?php echo $loc->getText("mbrViewBarcode"); ?>
      -->
        <div class="col-sm-5">

          <div class="input-group">
              
              <?php printInputText("holdBarcodeNmbr",18,18,$postVars,$pageErrors); ?>
            <span class="bottomalign input-group-btn">
              <input type="hidden" name="mbrid" value="<?php echo H($mbrid);?>">
              <input type="hidden" name="classification" value="<?php echo H($mbr->getClassification());?>">
              <input type="submit" value="<?php echo $loc->getText("mbrViewPlaceHold"); ?>" class="bottomalign btn btn-primary">
            </span>
          </div>
        </div>

        <script>
          $('#holdBarcodeNmbr').attr('placeholder','Número de copia');
        </script>

      </form>
    </div>
  </div>

    <!--<h1><?php echo $loc->getText("mbrViewHead6"); ?></h1>-->
    <table class="insidepanel nomarginbottom table">
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
          <?php echo date('d/m/y H:i',strtotime(($hold->getHoldBeginDt())));?>
        </td>
        <!--<td class="primary" valign="top">
          <img src="../images/<?php echo HURL($materialImageFiles[$hold->getMaterialCd()]);?>" width="20" height="20" border="0" align="middle" alt="<?php echo H($materialTypeDm[$hold->getMaterialCd()]);?>">
          <?php echo H($materialTypeDm[$hold->getMaterialCd()]);?>
        </td>-->
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
          <?php if ($hold->getDueBackDt() != '') echo date('d/m/y',strtotime(($hold->getDueBackDt())));?>
        </td>
        <td class="primary" valign="top" nowrap="yes">
          <a class="glyphicon glyphicon-trash" style="padding: 0px 10px;" href="../shared/hold_del_confirm.php?bibid=<?php echo HURL($hold->getBibid());?>&amp;copyid=<?php echo HURL($hold->getCopyid());?>&amp;holdid=<?php echo HURL($hold->getHoldid());?>&amp;mbrid=<?php echo HURL($mbrid);?>"><?php //echo $loc->getText("mbrViewDel"); ?></a>
        </td>
      </tr>
    <?php
        }
      }
      $holdQ->close();
    ?>


    </table>
  </div>

<!--****************************************************************************
    *  Panel de informacion de socio
    **************************************************************************** -->

  <div class="row">
  <div class="col-sm-8">


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
                            <a class="btn btn-primary"  href="../circ/mbr_pwd_reset_form.php?UID=<?php  echo HURL( H($mbr->getBarcodeNmbr()));?>&mbrid=<?php echo $mbrid;?>" class="<?php   echo H($row_class);?>"><?php  echo $loc->getText("Reset pass"); ?></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

<!--****************************************************************************
    *  Muestra imagen de usuario 
    *     Show imagen user
    *    Modificado Jose,  Lara joanlaga@hotmail.com
	*     <?php // Modificado para mostrar foto usuario ?>
    **************************************************************************** -->

<br>
<br>

<!--****************************************************************************
    *  Checkout form - Input prestar y Tabla prestados
    **************************************************************************** -->

</div>

<?php require_once("../shared/footer.php"); ?>
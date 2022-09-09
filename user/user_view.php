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

  $mbrid =$_SESSION["mbrid"];

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

<link rel="stylesheet" href="../css/material/material-kit.min.css">
<link rel="stylesheet" href="../css/material/material.min.css">

<script src="../css/material/material.min.js"></script>
<script src="../css/material/material-kit.min.js" type="text/javascript"></script>

  <script src="../css/material/core/jquery.min.js" type="text/javascript"></script>
  <script src="../css/material/core/popper.min.js" type="text/javascript"></script>
  <script src="../css/material/core/bootstrap-material-design.min.js" type="text/javascript"></script>
  <script src="../css/material/plugins/moment.min.js"></script>
  <!--  Plugin for the Datepicker, full documentation here: https://github.com/Eonasdan/bootstrap-datetimepicker -->
  <script src="../assets/js/plugins/bootstrap-datetimepicker.js" type="text/javascript"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="../css/material/plugins/nouislider.min.js" type="text/javascript"></script>
  <!-- Control Center for Now Ui Kit: parallax effects, scripts for the example pages etc -->
  <script src="../css/material/material-kit.js" type="text/javascript"></script>

<div id="demo-toast-example" class="mdl-js-snackbar mdl-snackbar">
  <div class="mdl-snackbar__text"></div>
  <button class="mdl-snackbar__action" type="button"></button>
</div>

<script type="text/javascript">

          function edit(msg) {
            r(function(){
                var snackbarContainer = document.querySelector('#demo-toast-example');
                var showToastButton = document.querySelector('#demo-show-toast');
                var data = {message: "msg"};
                snackbarContainer.MaterialSnackbar.showSnackbar(data);
            });
          }

          function edit() {
            r(function(){
                var snackbarContainer = document.querySelector('#demo-toast-example');
                var showToastButton = document.querySelector('#demo-show-toast');
                var data = {message: "Datos de socio actualizados correctamente"};
                snackbarContainer.MaterialSnackbar.showSnackbar(data);
            });
          }
            
          function r(f){/in/.test(document.readyState)?setTimeout('r('+f+')',9):f()}

          document.getElementById("body").classList.add('userhome');

          //var container = document.getElementById("container-fluid");
          //container.style.background = "rgba(0, 0, 0, 0.45)";
          
</script>

<?php

 #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  
  if (isset($_SESSION["msg"])) {
    //$msg = "<div class='margin30 nomarginbottom alert alert-info'>".H($_SESSION["msg"])."</div>";
    echo '<script type="text/javascript">', 'edit("'.$_SESSION["msg"].'")', '</script>';
  } else {
    $msg = "";
  }
  if (isset($_GET["msg"])) {
    //$msg = "<div class='margin30 nomarginbottom alert alert-info'>".H($_GET["msg"])."</div>";
    echo '<script type="text/javascript">', 'edit("'.$_GET["msg"].'")', '</script>';
  } else {
    $msg = "";
  }


echo $balMsg; 
?>


<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">


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


<style>
    .demo-card-photo.mdl-card {
      height: 370px;
    }
    .demo-card-wide.mdl-card {
      height: 370px;
    }
    .demo-card-photo > .mdl-card__title {
      color: #fff;
      height: 300px;
      <?php if  (H($mbr->getFoto())) { ?>
        background: url( <?php echo  ".." . FOTO_PATH ."/" . H($mbr->getFoto());?>);
      <?php } else { ?>
        background: rgb(223, 223, 223);
      <?php  } ?>
      
      background-size: 100%;
      background-position-y: 30%;
      background-position-x: center; 
    }
    .demo-card-photo > .mdl-card__menu {
      color: #fff;
    }
    .clearfix{
    clear:both
    }

    .tooltip.top {
      left: 964px !important;
    }

    .material-icons{
      margin: inherit;
    }

    .mdl-layout__drawer-button i {
      color: #000;
    }

</style>

<script type="text/javascript">

  $(document).ready(function() {
    $("#editar").click(function() {
      window.location.href = "../user/user_edit_form.php?mbrid=<?php echo HURL($mbrid);?>";
    });
    $("#cuenta").click(function() {
      window.location.href = "../user/user_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y";
    });
    $("#historial").click(function() {
      window.location.href = "../user/user_history.php?mbrid=<?php echo HURL($mbrid);?>";
    });

    $(document.body).on('click', '.mdl-layout__drawer-button', function() {
      $('#foto_oscura').fadeIn(135);
    });

    $(document.body).on('click', '.mdl-layout__obfuscator', function() {
      $('#foto_oscura').fadeOut(120);
    });

  });

</script>

 <div class="main main-raised" style="margin-top: 20px; background: #e8e8e8">
    <div class="profile-content">
        <div class="demo-layout-transparent mdl-layout mdl-js-layout">
          <div class="mdl-layout__drawer" style="border-radius: 6px">
          <span class="mdl-layout-title">Opciones</span>
          <nav class="mdl-navigation">
            <a class="mdl-navigation__link" style="background: #c5c5c5">Información</a>
            <a class="mdl-navigation__link" href="../user/user_edit_form.php?mbrid=<?php echo HURL($mbrid);?>">Editar datos</a>
            <a class="mdl-navigation__link" href="../user/user_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y">Cuenta</a>
            <a class="mdl-navigation__link" href="../user/user_history.php?mbrid=<?php echo HURL($mbrid);?>">Historial</a>
          </nav>
          </div>
        </div>
        <!--<button id="demo-menu-lower-right"
            class="mdl-button mdl-js-button mdl-button--icon" style="float: right; margin-top: 10px;
             margin-right: 10px">
          <i class="material-icons">more_vert</i>
        </button>-->

        <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
            for="demo-menu-lower-right">
          <li id="editar" class="mdl-menu__item">Editar datos</li>
          <li id="cuenta" class="mdl-menu__item">Cuenta</li>
          <li id="historial" class="mdl-menu__item">Historial</li>
        </ul>
      <div class="container">
        <div class="row" style="margin-left: 30px; margin-right: 30px">
          <div class="col-md-12 ml-auto mr-auto">
            <div class="profile">
                <?php if  (H($mbr->getFoto())) { ?>'              
                  <div class="avatar d-none d-md-block" style="height: 100px">
                    <img src="<?php echo  ".." . FOTO_PATH ."/" . H($mbr->getFoto());?>" alt="Circle Image" class="img-raised rounded-circle img-fluid" style="transform: translate3d(0,-50%,0); max-width: 200px; min-width: 200px; min-height: 200px; object-fit: cover; margin-left: 39%; position: absolute;">
                    <div id="foto_oscura" style="transform: translate3d(0,-100%,0); max-width: 200px; min-width: 200px; object-fit: cover; margin-left: 39%; position: absolute; overflow: hidden; max-height: 100px; filter: brightness(0.5); display: none;">
                      <img src=" ..' . FOTO_PATH .'/' . H($mbr->getFoto()) . '" alt="Circle Image" class="img-raised rounded-circle img-fluid" style="/*transform: translate3d(0,-50%,0); max-width: 200px; min-width: 200px; margin-left: 41%;*/ min-width: 200px; min-height: 200px; object-fit: cover;">
                    </div>
                  </div>'
                <?php } else { ?>
                  <div class="avatar d-none d-md-block" style="height: 100px">
                    <img src="../images/avatar_2x-100.png" alt="Circle Image" class="img-raised rounded-circle img-fluid" style="transform: translate3d(0,-50%,0); max-width: 200px; min-width: 200px; margin-left: 39%; position: absolute;">
                    <div id="foto_oscura" style="transform: translate3d(0,-100%,0); max-width: 200px; min-width: 200px; margin-left: 39%; position: absolute; overflow: hidden; max-height: 100px; filter: brightness(0.5); display: none;">
                      <img src="../images/avatar_2x-100.png" alt="Circle Image" class="img-raised rounded-circle img-fluid" style="/*transform: translate3d(0,-50%,0); max-width: 200px; margin-left: 41%;*/ min-width: 200px;">
                    </div>
                  </div>
                <?php } ?>

              <div class="name" style="text-align: center;">
                <h2><?php echo " "; echo H($mbr->getFirstName());?> <?php echo H($mbr->getLastName());?></h2>
              
              </div>
            </div>
          </div>
        </div>
        <div class="description text-center">
          <w style="line-height: 20px">En la página de la biblioteca podés renovar préstamos, ver y cancelar tus reservas, y editar tus datos.</w>
          <?php if  (!H($mbr->getFoto())) { echo("<w style='line-height: 20px'> También podés agregar una foto de perfil.</w>"); }?>
        </div>
        <div class="row">
          <div class="col-md-12 ml-auto mr-auto" style="margin-top: 30px">
            <div class="profile-tabs">
              <ul class="nav nav-pills nav-pills-icons justify-content-center" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#prestado" role="tab" data-toggle="tab">
                    <i class="material-icons">local_library</i> Prestado
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#reservado" role="tab" data-toggle="tab">
                    <i class="material-icons">library_books</i> Reservado
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#datos" role="tab" data-toggle="tab">
                    <i class="material-icons">person</i> Datos
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="tab-content tab-space">
          <div class="tab-pane text-center gallery" id="reservado">
            <div class="row">
              <div class="col-md-12 ml-auto">

<!--****************************************************************************
    *  Hold form - Reservar
    **************************************************************************** -->

  <div class="mdl-card mdl-shadow--2dp" style="width: 100%; margin-top: 20px">
  <div class="mdl-card__title">
    <h2 class="mdl-card__title-text" style="font-size: 32">
    <?php echo $loc->getText("mbrViewHead6"); ?>
    </h2>
  </div>

<table class="nomarginbottom nomargin table" style="margin: 0px 10px 10px 10px; width: 98%">

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
      <a data-toggle="tooltip" title="Eliminar" class="glyphicon glyphicon-trash" href="../shared/hold_del_confirm.php?bibid=<?php echo HURL($hold->getBibid());?>&amp;copyid=<?php echo HURL($hold->getCopyid());?>&amp;holdid=<?php echo HURL($hold->getHoldid());?>&amp;mbrid=<?php echo HURL($mbrid);?>&mode=1"><?php //echo $loc->getText("mbrViewDel"); ?></a>
    </td>
  </tr>
<?php
    }
  }
  $holdQ->close();
?>
</table>


</div> <!--Tarjeta-->
              </div>
            </div>
          </div>
          <div class="tab-pane active text-center gallery" id="prestado">
            <div class="row">
              <div class="col-md-12 ml-auto">
                <div class="mdl-card mdl-shadow--2dp" style="width: 100%; margin-top: 20px">

<!--****************************************************************************
    *  Checkout form
    **************************************************************************** -->

  <div class="mdl-card__title">
    <h2 class="mdl-card__title-text" style="font-size: 32">
    <?php echo $loc->getText("mbrViewHead4"); ?>
    </h2>
  </div>


 
<table class="nomarginbottom nomargin table" style="margin: 0px 10px 0px 10px; width: 98%">
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
      <?php echo $loc->getText("mbrViewOutHdr7"); ?>
    </th>
    <th valign="top" align="left">
      <?php echo $loc->getText("mbrViewOutHdr8"); ?>
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
    <td nowrap class="primary" valign="top" >
      <?php echo H($biblio->getDaysLate());?> Día/s
    </td>
    <td nowrap class="primary" valign="top" >
      <a data-toggle="tooltip" title="Renovar" class="glyphicon glyphicon-refresh" href="../user/checkout.php?barcodeNmbr=<?php echo HURL($biblio->getBarcodeNmbr());?>&amp;mbrid=<?php echo HURL($mbrid);?>&amp;renewal"></a>
      <?php
        if($biblio->getRenewalCount() > 0) { ?>
          
          <?php echo H($biblio->getRenewalCount()/24);?> <?php echo $loc->getText("mbrViewOutHdr9"); ?>
      <?php
        } ?>
    </td>
  </tr>
<?php
    }
  }
  $biblioQ->close();
?>
</table>
<br>
</font>

</div> <!--Tarjeta-->

<!--****************************************************************************
    *  Datos
    **************************************************************************** -->

              </div>
            </div>
          </div>
          <div class="tab-pane text-center gallery" id="datos">
            <div class="row">
              <div class="col-md-12 ml-auto">


      <div class="demo-card-wide mdl-card mdl-shadow--2dp offset-md-4">
        <div class="mdl-card__title">
          <h2 class="mdl-card__title-text">Tus datos</h2>
        </div>
        <div class="mdl-card__supporting-text">
          <div class=" col-md-10 col-lg-10">
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
                                              echo $mbr->getHomePhone()."</br> ";
                                            }
                                            if ($mbr->getWorkPhone() != "") {
                                              echo $loc->getText("mbrViewPhoneWork")." ".$mbr->getWorkPhone()."</br> ";
                                            }
                                            if ($mbr->getCel() != "") {
                                              echo $loc->getText("mbrViewCel")." ".$mbr->getCel();
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
        <div class="mdl-card__actions mdl-card--border">
          <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" style="font-weight: bold; float: left; text-decoration: none;" href="../user/user_pwd_reset_form.php?UID=<?php  echo HURL( H($mbr->getBarcodeNmbr()));?>">
            Cambiar contraseña 
          </a>
        </div>
        <div class="mdl-card__menu">
          <a class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect" href="javascript:popSecondary('../user/user_print_carnet.php?mbrid=<?php echo H(addslashes(U($mbrid)));?>')">
            <i class="material-icons">print</i>
          </a>
        </div>
      </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



<?php printInputText("barcodeNmbr",18,30,$postVars,$pageErrors,null,"hidden"); ?>

<div style="margin-top: 40px">
</div> <!--Row-->

<?php require_once("../shared/footer.php"); ?>
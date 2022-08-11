<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "checkin";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcodeNmbr";

  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  
  $barcode = $_GET["barcode"];
  if (isset($_GET["mbrid"])) {
    $mbrid = $_GET["mbrid"];
  }
  
  if (isset($_SESSION['feeMsg'])) {
    echo '<p><font class="error">'.$_SESSION['feeMsg'].'</font></p>';
    unset($_SESSION['feeMsg']);
  }
?>
<h2 style="font-family: Roboto"><?php echo $loc->getText("holdMessageHdr"); ?></h2>
<?php echo $loc->getText("holdMessageMsg1",array("barcode"=>$barcode)); ?>
<br><br>
<a href="../circ/checkin_form.php"><?php echo $loc->getText("holdMessageMsg2"); ?></a>
<br>
<?php 
if (isset($mbrid)) {
  // Si tenemos seteado un mbrid, es porque se devolvió un préstamo con reserva desde la página del usuario
  // Le ofrecemos el botón para volver a esa página, ver shelving_cart.php
  echo '<a href="../circ/mbr_view.php?mbrid=' . $mbrid . '">' . $loc->getText("holdMessageMsgMbrid") . '</a>';
}
?>
<?php require_once("../shared/footer.php"); ?>

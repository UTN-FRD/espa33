<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  #****************************************************************************
  #*  Checking for get vars.
  #****************************************************************************
  $mode = $_GET["mode"];
  $bibid = $_GET["bibid"];
  $copyid = $_GET["copyid"];
  $holdid = $_GET["holdid"];
  if (isset($_GET["mbrid"])) {
    $mbrid = $_GET["mbrid"];
    $tab = "cataloging";
    $nav = "view";
    $returnUrl = "../circ/mbr_view.php?mbrid=".$mbrid;
  } else {
    $mbrid = "";
    $tab = "cataloging";
    $nav = "holds";
    $returnUrl = "../catalog/biblio_hold_list.php?bibid=".$bibid;
  }
  
  $restrictInDemo = TRUE;
  //require_once("../shared/logincheck.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,"shared");

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  if ($mode == '1') {
    require_once("../user/logincheck.php");
    $tab = "user";
    require_once("../opac/header_opac.php");
  } else {
    require_once("../shared/logincheck.php");
    require_once("../shared/header.php");
  }
?>
<center>
<form name="delbiblioform" method="POST" action="<?php echo H($returnUrl);?>">
<br>
<h5><?php echo $loc->getText("holdDelConfirmMsg"); ?></h5>
<br>
      <input type="button" onClick="self.location='../shared/hold_del.php?bibid=<?php echo H(addslashes(U($bibid)));?>&amp;copyid=<?php echo H(addslashes(U($copyid)));?>&amp;holdid=<?php echo H(addslashes(U($holdid)));?>&amp;mbrid=<?php echo H(addslashes(U($mbrid)));?>&amp;mode=<?php echo $mode; ?>'" value="<?php echo $loc->getText("sharedDelete"); ?>" class="btn btn-primary">
      <input type="submit" value="<?php echo $loc->getText("sharedCancel"); ?>" class="btn btn-default">
</form>
</center>

<?php include("../shared/footer.php");?>

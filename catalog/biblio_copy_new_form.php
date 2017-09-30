<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "cataloging";
  $nav = "newcopy";
  $helpPage = "biblioCopyEdit";
  $focus_form_name = "newCopyForm";
  $focus_form_field = "barcodeNmbr";

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_GET) == 0) {
    header("Location: ../catalog/index.php");
    exit();
  }

  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/get_form_vars.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $bibid = $_GET["bibid"];
  require_once("../shared/header.php");


?>

<div class="col col-md-5">

  <form name="newCopyForm" method="POST" action="../catalog/biblio_copy_new.php<?php echo empty($_GET['hits'])?'':'?hits='.$_GET['hits'].(empty($_GET['isbn'])?'':'&isbn='.$_GET['isbn']) ?>">

        <h3><?php echo $loc->getText("biblioCopyNewFormLabel"); ?></h3>
      
        <div style="margin-bottom: 10"><?php printInputText("barcodeNmbr",20,20,$postVars,$pageErrors); ?></div>
        
        <div style="margin-bottom: 10"><?php printInputText("copyDesc",40,40,$postVars,$pageErrors); ?></div>
        
        <input type="submit" value="<?php echo $loc->getText("catalogSubmit"); ?>" class="btn btn-primary">
        <input type="button" onClick="self.location='../shared/biblio_view.php?bibid=<?php echo HURL($bibid); ?>'" value="<?php echo $loc->getText("catalogCancel"); ?>" class="btn btn-default">
        <input type="hidden" name="bibid" value="<?php echo H($bibid);?>">

  </form>

</div>

<script type="text/javascript">
  $('#barcodeNmbr').attr('placeholder','<?php echo $loc->getText("biblioCopyNewBarcode");?>');
  $('#copyDesc').attr('placeholder','<?php echo $loc->getText("biblioCopyNewDesc");?>');
</script>

<?php include("../shared/footer.php"); ?>

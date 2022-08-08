<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../functions/inputFuncs.php");
 require_once('../classes/DmQuery.php');
  $dmQ = new DmQuery();
  $dmQ->connect();
  $mbrClassifyDm = $dmQ->getAssoc('mbr_classify_dm');
  $mbrStatusDm = array("y" => $loc->getText("mbrActive"), "n" => $loc->getText("mbrInactive"));
  $customFields = $dmQ->getAssoc('member_fields_dm');
  $dmQ->close();

  // Get & show the latest BarcodeNumber.
  require_once("../shared/common.php");
  require_once("../classes/Query.php");

  $fields = array(
    //"mbrFldsClassify"  => inputField('hidden',   "classification", $mbr->getClassification(), NULL, $mbrClassifyDm),
    //"mbrFldsStatus"    => inputField('hidden',   "status",         $mbr->getStatus(), NULL, $mbrStatusDm),
    //"mbrFldsCardNmbr"  => inputField('hidden',     "barcodeNmbr"   , $mbr->getBarcodeNmbr(), NULL, NULL, $barcode_help),      
    "mbrFldsLastName"  => inputField('text',     "lastName",       $mbr->getLastName()),
    "mbrFldsFirstName" => inputField('text',     "firstName",      $mbr->getFirstName()),
    //"Dni"              => inputField('hidden',     "dni",            $mbr->getDni()),
    //"Legajo"           => inputField('hidden',     "legajo",         $mbr->getLegajo()),
    "mbrFldsHomePhone" => inputField('text',     "homePhone",      $mbr->getHomePhone()),
    "mbrFldsWorkPhone" => inputField('text',     "workPhone",      $mbr->getWorkPhone()),
    "mbrFldsCel"       => inputField('text',     "cel",            $mbr->getCel()),
    "mbrFldsEmail"     => inputField('text',     "email",          $mbr->getEmail()),
    "mbrFldsFoto"      => inputField('hidden',   "foto",           $mbr->getFoto()),
    "MailingAddress:"  => inputField('textarea', "address",        $mbr->getAddress()),
    //"mbrFldsPassUser"  => inputField('hidden',     "passUser",       $mbr->getPassUser()),
    "mbrFldsBornDt"    => inputField('text',     "bornDt",         $mbr->getBornDt()),
    //"mbrFldsOther"     => inputField('hidden', "other",          $mbr->getOther()),
  );
 foreach ($customFields as $name => $title) {
   //$fields[$title.':'] = inputField('hidden', 'custom_'.$name, $mbr->getCustom($name));
  }
?>

<h3><?php echo H($headerWording);?> <?php echo $loc->getText("mbrFldsHeader"); ?></h3>
<hr style="margin-bottom: 15">

<table class="nomargin table" style="width: 70%">
<?php
  foreach ($fields as $title => $html) {
?>
  <tr id="<?php echo "$title"; ?>">
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText($title); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $html; ?>
    </td>
  </tr>
<?php
  }
?>

  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("Seleccione_Foto"); ?>
    </td>
    <td valign="top" class="primary">
	<input type="file" name="foto" >
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input class="btn btn-primary" type="submit" value="<?php echo $loc->getText("mbrFldsSubmit"); ?>">
      <input type="button" onClick="self.location='<?php echo H(addslashes($cancelLocation));?>'" value="<?php echo $loc->getText("mbrFldsCancel"); ?>" class="btn btn-default">
    </td>
  </tr>
</table>

<script type="text/javascript">
   $('#bornDt').attr('placeholder','AAAA-MM-DD');
   $('#mbrFldsFoto').attr('style','display: none');
</script>
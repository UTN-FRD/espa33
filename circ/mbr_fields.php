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

  $barcode = "0";
  $sql = "SELECT MAX(barcode_nmbr) AS bn FROM member"; 
  $q = new Query(); 
  $q->connect();
  $rows = $q->exec($sql);
  if (count($rows) > 0) {
    $barcode = $rows[0]["bn"];
  }
  $q->close();
  $barcode_help = $loc->getText("mbrLatestBarcode").": ". $barcode;
  /*$barcode_help .= '<input type="checkbox" id="chk_auto_barcode" name="chk_auto_barcode" value="1" /> '.
  $loc->getText("mbrAutoBarcode"); No funciona*/

  $fields = array(
    "mbrFldsClassify"  => inputField('select',   "classification", $mbr->getClassification(), NULL, $mbrClassifyDm),
    "mbrFldsStatus"    => inputField('select',   "status",         $mbr->getStatus(), NULL, $mbrStatusDm),
    "mbrFldsCardNmbr"  => inputField('text',     "barcodeNmbr"   , $mbr->getBarcodeNmbr(), NULL, NULL),
    "mbrFldsLastName"  => inputField('text',     "lastName",       $mbr->getLastName()),
    "mbrFldsFirstName" => inputField('text',     "firstName",      $mbr->getFirstName()),
    "Dni:"             => inputField('text',     "dni",            $mbr->getDni()),
    "Legajo:"          => inputField('text',     "legajo",         $mbr->getLegajo()),
    "mbrFldsHomePhone" => inputField('text',     "homePhone",      $mbr->getHomePhone()),
    "mbrFldsWorkPhone" => inputField('text',     "workPhone",      $mbr->getWorkPhone()),
    "mbrFldsCel"       => inputField('text',     "cel",            $mbr->getCel()),
    "mbrFldsEmail"     => inputField('text',     "email",          $mbr->getEmail()),
    "mbrFldsFoto"      => inputField('text',     "foto",           $mbr->getFoto()),
    "MailingAddress:"  => inputField('text',     "address",        $mbr->getAddress()),
    "Ciudad:"          => inputField('text',     "city",           $mbr->getCity()),
    "mbrFldsPassUser"  => inputField('text',     "passUser",       $mbr->getPassUser()),
    "mbrFldsBornDt"    => inputField('text',     "bornDt",         $mbr->getBornDt()),
    "mbrFldsOther"     => inputField('textarea', "other",          $mbr->getOther()),
  );
  foreach ($customFields as $name => $title) {
    $fields[$title.':'] = inputField('text', 'custom_'.$name, $mbr->getCustom($name));
  }
?>

<table class="table member_fields" style="width: 70%">
  <tr>
    <th>
      <?php echo H($headerWording);?> <?php echo $loc->getText("mbrFldsHeader"); ?>
    </td>
  </tr>
<?php
  foreach ($fields as $title => $html) {
?>
  <tr>
    <td>
      <?php echo $loc->getText($title); ?>
    </td>
    <td>
      <?php echo $html; ?>
    </td>
  </tr>
<?php
  }
?>

  <tr>
    <td>
      <?php echo $loc->getText("Seleccione_Foto"); ?>
    </td>
    <td>
	<input class="btn btn-default" type="file" name="foto" >
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input class="btn btn-primary" type="submit" value="<?php echo $loc->getText("mbrFldsSubmit"); ?>" class="button">
      <input class="btn btn-primary" type="button" onClick="self.location='<?php echo H(addslashes($cancelLocation));?>'" value="<?php echo $loc->getText("mbrFldsCancel"); ?>" class="button">
    </td>
  </tr>
</table>

<script type="text/javascript">
   $('#bornDt').attr('placeholder','AAAA-MM-DD');
   $('#barcodeNmbr').attr('placeholder','<?php echo $barcode_help;?>');
</script>
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../classes/DmQuery.php");
  require_once("../classes/UsmarcTagDm.php");
  require_once("../classes/UsmarcTagDmQuery.php");
  require_once("../classes/UsmarcSubfieldDm.php");
  require_once("../classes/UsmarcSubfieldDmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../functions/inputFuncs.php");
  require_once("../catalog/inputFuncs.php");

  #****************************************************************************
  #*  Loading up an array ($marcArray) with the USMarc tag descriptions.
  #****************************************************************************

  $marcTagDmQ = new UsmarcTagDmQuery();
  $marcTagDmQ->connect();
  if ($marcTagDmQ->errorOccurred()) {
    $marcTagDmQ->close();
    displayErrorPage($marcTagDmQ);
  }
  $marcTagDmQ->execSelect();
  if ($marcTagDmQ->errorOccurred()) {
    $marcTagDmQ->close();
    displayErrorPage($marcTagDmQ);
  }
  $marcTags = $marcTagDmQ->fetchRows();
  $marcTagDmQ->close();

  $marcSubfldDmQ = new UsmarcSubfieldDmQuery();
  $marcSubfldDmQ->connect();
  if ($marcSubfldDmQ->errorOccurred()) {
    $marcSubfldDmQ->close();
    displayErrorPage($marcSubfldDmQ);
  }
  $marcSubfldDmQ->execSelect();
  if ($marcSubfldDmQ->errorOccurred()) {
    $marcSubfldDmQ->close();
    displayErrorPage($marcSubfldDmQ);
  }
  $marcSubflds = $marcSubfldDmQ->fetchRows();
  $marcSubfldDmQ->close();

?>

<input type="hidden" name="posted" value="1" />


<table class="table">
  <tr>
    <th colspan="2" valign="top" nowrap="yes" align="left">
      <?php
        echo H($headerWording)." ";
        echo $loc->getText("biblioFieldsLabel");
      ?>:
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary form-required">
      <sup>*</sup> <?php echo $loc->getText("biblioFieldsMaterialTyp"); ?>:
    </td>
    <td valign="top" class="primary">
<?php
  //    Played with printselect function
  if (isset($postVars['materialCd'])) {
    $materialCd = $postVars['materialCd'];
  } else {
    $materialCd = '';
  }
  $fieldname="materialCd";
  $domainTable="material_type_dm";

  $dmQ = new DmQuery();
  $dmQ->connect();
  $dms = $dmQ->get($domainTable);
  $dmQ->close();
  echo "<select class=\"form-control\" id=\"materialCd\" name=\"materialCd\"";

  //    Needed OnChange event here.
  echo " onChange=\"matCdReload()\">\n";
  foreach ($dms as $dm) {
    echo "<option value=\"".H($dm->getCode())."\"";
    if (($materialCd == "") && ($dm->getDefaultFlg() == 'Y')) {
      $materialCd = $dm->getCode();
      echo " selected";
    } elseif ($materialCd == $dm->getCode()) {
      echo " selected";
    }
    echo ">".H($dm->getDescription())."</option>\n";
  }
  echo "</select>\n";
?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary form-required">
      <sup>*</sup> <?php echo $loc->getText("biblioFieldsCollection"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printSelect("collectionCd","collection_dm",$postVars); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary form-required" valign="top">
      <sup>*</sup> <?php echo $loc->getText("biblioFieldsCallNmbr"); ?>:
    </td>
    <td valign="top" class="primary">
      <?php printInputText("callNmbr1",20,20,$postVars,$pageErrors); ?><br>
      <?php printInputText("callNmbr2",20,20,$postVars,$pageErrors); ?><br>
      <?php printInputText("callNmbr3",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $loc->getText("biblioFieldsOpacFlg"); ?>:
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="opacFlg" value="CHECKED"
        <?php if (isset($postVars["opacFlg"])) echo H($postVars["opacFlg"]); ?> >
    </td>
  </tr>

  <tr>
    <td colspan="2" nowrap="true" class="primary">
      <b><?php echo $loc->getText("biblioFieldsUsmarcFields"); ?>:</b>
    </td>
  </tr>
  <?php printUsmarcInputText(245,"a",TRUE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(245,"b",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(245,"c",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(100,"a",TRUE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(650,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(650,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"1");?>
  <?php printUsmarcInputText(650,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"2");?>
  <?php printUsmarcInputText(650,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"3");?>
  <?php printUsmarcInputText(650,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL,"4");?>
  <?php printUsmarcInputText(250,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(10,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(20,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(50,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(50,"b",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(80,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(80,"b",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(80,"x",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(80,"2",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(80,"6",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(80,"8",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(82,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(82,"2",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(260,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(260,"b",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(260,"c",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(520,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXTAREA_CNTRL);?>
  <?php printUsmarcInputText(300,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(300,"b",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(300,"c",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(300,"e",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, TRUE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(20,"c",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(541,"h",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_TEXT_CNTRL);?>
  <?php printUsmarcInputText(902,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_FILE_CNTRL);?>
  <?php printUsmarcInputText(902,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_FILE_CNTRLS,'');?>
  <?php printUsmarcInputText(902,"c",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_FILE_CNTRLB);?>
  <?php printUsmarcInputText(902,"c",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_FILE_CNTRLA,'');?>
  <?php printUsmarcInputText(903,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_FILE_CNTRLK);?>
  <?php printUsmarcInputText(903,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_FILE_CNTRLD,'');?>
  <?php printUsmarcInputText(256,"a",FALSE,$postVars,$pageErrors,$marcTags, $marcSubflds, FALSE,OBIB_FILE_CNTRLF,'');?>

    <?php include("biblio_custom_fields.php");?>

    <tr>
    <td align="center" colspan="2" class="primary">
      <input class="btn btn-primary" type="submit" value="<?php echo $loc->getText("catalogSubmit"); ?>">
      <input class="btn btn-primary" type="button" onClick="self.location='<?php echo H(addslashes($cancelLocation));?>'" value="<?php echo $loc->getText("catalogCancel"); ?>">
    </td>
  </tr>

    <tr>
    <td align="Left" colspan="2" class="primary">

   <sup> (Notas) </sup><li><?php echo $loc->getText("biblioFieldFile"); ?></li></br>
   <li><sup> (1) </sup><?php echo $loc->getText("biblioFieldsNote3") . COVER_PATH ; ?></li>
   <li><sup> (2) </sup><?php echo $loc->getText("biblioFieldsNote1"); ?></li>
   <li><sup> (3) </sup><?php echo $loc->getText("biblioFieldsNote2"); ?></li><?php echo  AUTOR_PATH ; ?>
   <li><sup> (4) </sup><?php echo $loc->getText("biblioFieldsNote5") .  DIGI_PATH ; ?></li>
   <li><sup> (5) </sup><?php echo $loc->getText("biblioFieldsNote4"); ?></li>

   <li><sup> (6) </sup><?php echo $loc->getText("biblioFieldsNote6"); ?></li>
    <font class="small">
      <?php echo $loc->getText("catalogFootnote",array("symbol"=>"*")); ?>
    </font>
    <?php //modificado jalg para subir archivo digital ?>
    </td>
  </tr>
</table>

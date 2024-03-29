<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "admin";
  $nav = "settings";
  $focus_form_name = "editsettingsform";
  $focus_form_field = "libraryName";

  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for query string flag to read data from database.
  #****************************************************************************
  if (isset($_GET["reset"])){
    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);

    include_once("../classes/Settings.php");
    include_once("../classes/SettingsQuery.php");
    include_once("../functions/errorFuncs.php");
    $setQ = new SettingsQuery();
    $setQ->connect();
    if ($setQ->errorOccurred()) {
      $setQ->close();
      displayErrorPage($setQ);
    }
    $setQ->execSelect();
    if ($setQ->errorOccurred()) {
      $setQ->close();
      displayErrorPage($setQ);
    }
    $set = $setQ->fetchRow();
    $postVars["libraryName"] = $set->getLibraryName();
    $postVars["libraryImageUrl"] = $set->getLibraryImageUrl();
    if ($set->isUseImageSet()) {
      $postVars["isUseImageSet"] = "CHECKED";
    } else {
      $postVars["isUseImageSet"] = "";
    }
    $postVars["libraryHours"] = $set->getLibraryHours();
    $postVars["libraryAders"] = $set->getLibraryAders();
    $postVars["libraryPhone"] = $set->getLibraryPhone();
    $postVars["libraryUrl"] = $set->getLibraryUrl();
    $postVars["opacUrl"] = $set->getOpacUrl();
    $postVars["sessionTimeout"] = $set->getSessionTimeout();
    $postVars["itemsPerPage"] = $set->getItemsPerPage();
    $postVars["purgeHistoryAfterMonths"] = $set->getPurgeHistoryAfterMonths();
    if ($set->isBlockCheckoutsWhenFinesDue()) {
      $postVars["isBlockCheckoutsWhenFinesDue"] = "CHECKED";
    } else {
      $postVars["isBlockCheckoutsWhenFinesDue"] = "";
    }
    $postVars["holdMaxDays"] = $set->getHoldMaxDays();
    $postVars["locale"] = $set->getLocale();
    $postVars["charset"] = $set->getCharset();
    $postVars["htmlLangAttr"] = $set->getHtmlLangAttr();
    $postVars["fontNormal"] = $set->getFontNormal();
    $postVars["fontSize"] = $set->getFontSize();
    $postVars["inactiveMemberAfterDays"] = $set->getInactiveMemberAfterDays();
    
    $setQ->close();
  } else {
    require("../shared/get_form_vars.php");
  }

  // Generate font selections
  function getFontSelections($name, $postVars) {
    $options = '<select name="' . $name . '">';
    if ($handle = opendir('../font')) {
      while(false !== ($file = readdir($handle))) {
        $f = explode('.', $file);
        
        // Implemented font files
        if (isset($f[1]) && $f[1] == 'php') {
          $opts[] = $f[0];
        }
      }
      
      sort($opts);
      $fontName = '';
      foreach ($opts as $val) {
        // Ignore related fonts ( [font-name]b/i/bi )
        if ($fontName != $val) {
          if (empty($fontName)) {
            $fontName = $val;
          }
          else if ($fontName != substr($val, 0, strlen($fontName))) {
            $fontName = $val;
          }
          else if (in_array(substr($val, strlen($fontName)), array('b','i','bi'))) {
            continue;
          }
        }
        $options .= "<option value=\"{$val}\" " . ($val == $postVars[$name] ? 'selected="selected"' : '') . ">{$val}</option>\n";
      }
    }
    else {
      $options = "<option value=\"\">-- No font installed --</option>\n";
    }
    
    return $options . "</select>\n";
  }

  #****************************************************************************
  #*  Display update message if coming from settings_edit with a successful update.
  #****************************************************************************
  if (isset($_GET["updated"])){
?>
  <div class='margin30 nomarginbottom alert alert-info'><?php echo $loc->getText("admin_settingsUpdated"); ?></div>
<?php
  }
?>

<form name="editsettingsform" method="POST" action="../admin/settings_edit.php">
<input type="hidden" name="code" value="<?php if (isset($postVars["code"])) {echo H($postVars["code"]);}?>">
<table class="table">
  <tr>
    <th colspan="2" nowrap="yes" align="left">
      <?php echo $loc->getText("admin_settingsEditsettings"); ?>
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsLibName"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryName",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsLibimageurl"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryImageUrl",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsOnlyshowimginheader"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="isUseImageSet" value="CHECKED"
        <?php if (isset($postVars["isUseImageSet"])) echo H($postVars["isUseImageSet"]); ?> >
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsLibhours"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryHours",40,128,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsLibaders"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryAders",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsLibphone"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryPhone",40,40,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsLibURL"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("libraryUrl",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsOPACURL"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("opacUrl",40,300,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
     <?php echo $loc->getText("admin_settingsSessionTimeout"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("sessionTimeout",6,6,$postVars,$pageErrors); ?> <?php echo $loc->getText("admin_settingsMinutes"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsSearchResults"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("itemsPerPage",2,2,$postVars,$pageErrors); ?><?php echo $loc->getText("admin_settingsItemsperpage"); ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("admin_settingsPurgebibhistory"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("purgeHistoryAfterMonths",2,2,$postVars,$pageErrors); ?><?php echo $loc->getText("admin_settingsmonths"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsBlockCheckouts"); ?>
    </td>
    <td valign="top" class="primary">
      <input type="checkbox" name="isBlockCheckoutsWhenFinesDue" value="CHECKED"
        <?php if (isset($postVars["isBlockCheckoutsWhenFinesDue"])) echo H($postVars["isBlockCheckoutsWhenFinesDue"]); ?> >
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("Max. hold length:"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("holdMaxDays",2,2,$postVars,$pageErrors); ?><?php echo $loc->getText("days"); ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      <?php echo $loc->getText("admin_settingsLocale"); ?>
    </td>
    <td valign="top" class="primary">
      <select class="form-control" name="locale">
        <?php
          $stng = new Settings();
          $arr_lang = $stng->getLocales();
          foreach ($arr_lang as $langCode => $langDesc) {
            echo "<option value=\"".H($langCode)."\"";
            if ($langCode == $postVars["locale"]) {
              echo " selected";
            }
            echo ">".H($langDesc)."</option>\n";
          }
        ?>
      </select>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsHTMLChar"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("charset",20,20,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsHTMLTagLangAttr"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("htmlLangAttr",8,8,$postVars,$pageErrors); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsInactiveDays"); ?>
    </td>
    <td valign="top" class="primary">
      <?php printInputText("inactiveMemberAfterDays",2,2,$postVars,$pageErrors); ?><?php echo $loc->getText("days"); ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsFontNormal"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo getFontSelections('fontNormal', $postVars) ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      <?php echo $loc->getText("admin_settingsFontSize"); ?>
    </td>
    <td valign="top" class="primary">
      <?php echo printInputText('fontSize', 2, 2, $postVars, $pageErrors) ?> <?php echo $loc->getText("pt"); ?>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2" class="primary">
      <input type="submit" value="  <?php echo $loc->getText("adminUpdate"); ?>  " class="btn btn-primary">
    </td>
  </tr>

</table>
      </form>


<?php include("../shared/footer.php"); ?>

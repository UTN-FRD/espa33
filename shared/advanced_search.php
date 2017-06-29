<?php
  require_once("../classes/DmQuery.php");
  require_once("../shared/forms.php");
  require_once("../functions/inputFuncs.php");

  $nav = "search";
  if ($tab != "opac") {
    require_once("../shared/logincheck.php");
  }

  $lookup = "N";
  if (isset($_GET["lookup"])) {
    $lookup = "Y";
    $helpPage = "opacLookup";
  }

  $locTab = new Localize(OBIB_LOCALE, $tab);
  $loc = new Localize(OBIB_LOCALE, "shared");
?>


<!--
<div class="advanced-search">
  <form method="post" action="../shared/biblio_search.php">
    <fieldset id="advanced-search" class="collapsible">
      <legend><span id="legend-toggle">[+]</span> <?php echo $loc->getText("advsAdvancedSearch") ?></legend>
      <table class="table_advanced_search_1">
        <tr>
          <td>
            <span id="form_biblio_search_keyword_types_1"></span>
            <span id="form_biblio_search_keyword_text_1"></span>
          </td>
        </tr>
        <tr>
          <td>
            <span id="form_biblio_search_expressions_2"></span>
            <span id="form_biblio_search_keyword_types_2"></span>
            <span id="form_biblio_search_keyword_text_2"></span>
            <span id="form_biblio_search_add_field_2"></span>
          </td>
        </tr>
      </table>

      <table class="table_advanced_search_2">
        <tr>
          <td class="label"><?php echo $loc->getText("advsPublishedYear") ?>:</td>
          <td><input type="text" name="publishedYear" /></td>
        </tr>
        <tr>
          <td class="label"><?php echo $loc->getText("advsMaterialType"); ?>:</td>
          <td><?php echo form_biblio_material_types($loc); ?></td>
        </tr>
        <tr>
          <td class="label"><?php echo $loc->getText("advsCollectionType") ?>:</td>
          <td><?php printSelect("collectionCd", "collection_dm", $_POST, FALSE, FALSE); ?></td>
        </tr>
        <tr>
          <td colspan="2" height="20">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>
            <input type="hidden" name="searchType" value="advanced">
            <input type="hidden" name="sortBy" value="default">
            <input type="hidden" name="tab" value="<?php echo H($tab); ?>">
            <input type="hidden" name="lookup" value="<?php echo H($lookup); ?>">
            <input type="submit" value="<?php echo $loc->getText("advsSearch"); ?>" class="button" />
            <input type="reset" value="<?php echo $loc->getText("advsClear"); ?>" id="btn-reset" class="button" />
            <?php echo $loc->getText("or"); ?>
            <a href="/"><?php echo $loc->getText("cancel"); ?></a>
          </td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>
-->

  
<div class="row" style="margin-top: 20px">
  <div class="col-lg-6">

    <div class="panel panel-default">
      <div class="panel-heading"><?php echo $loc->getText("advsAdvancedSearch") ?></div>
      <div class="panel-body" style="padding: 10px 30px;">


        <form method="post" action="../shared/biblio_search.php">

       
            <div class="row">
              <tr>
                <td class="label"><?php echo $loc->getText("advsPublishedYear") ?>:</td>
                <td><input class="form-control" type="text" name="publishedYear" /></td>
              </tr>
            </div>
            <div class="row">
              <tr>
                <td class="label"><?php echo $loc->getText("advsMaterialType"); ?>:</td>
                <td><?php echo form_biblio_material_types($loc); ?></td>
              </tr>
            </div>
            <div class="row">
              <tr>
                <td class="label"><?php echo $loc->getText("advsCollectionType") ?>:</td>
                <td><?php printSelect("collectionCd", "collection_dm", $_POST, FALSE, FALSE); ?></td>
              </tr>
            </div>
            <tr>
              <td colspan="2" height="20">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>

              <div class="row">
                <div class="col-sm-offset-1 col-sm-10 text-center">
                <input type="hidden" name="searchType" value="advanced">
                <input type="hidden" name="sortBy" value="default">
                <input type="hidden" name="tab" value="<?php echo H($tab); ?>">
                <input type="hidden" name="lookup" value="<?php echo H($lookup); ?>">
                <input type="submit" value="<?php echo $loc->getText("advsSearch"); ?>" class="btn btn-primary" />
                <input type="reset" value="<?php echo $loc->getText("advsClear"); ?>" id="btn-reset" class="btn btn-primary"/>
                </div>
              </div>
         
              </td>
            </tr>
  
          </form>
      </div>
    </div>
  </div>
</div>
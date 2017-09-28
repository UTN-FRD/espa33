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



<div class="row" id="advsch">
  <div class="col-lg-6">

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><?php echo $loc->getText("advsAdvancedSearch") ?></h3>
      </div>
      <div class="panel-body" id="advsch-panelint">


        <form method="post" action="../shared/biblio_search.php">

       
            <div class="row">
              <tr>
                <td class="label"><?php echo $loc->getText("advsPublishedYear") ?>:</td>
                <td><input class="form-control" type="text" name="publishedYear" placeholder="Año" /></td>
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
                <input type="reset" value="<?php echo $loc->getText("advsClear"); ?>" id="btn-reset" class="btn btn-default"/>
                </div>
              </div>
         
              </td>
            </tr>
  
          </form>
        <br>
      </div>
    </div>
  </div>
</div>
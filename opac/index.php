<?php 
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "opac";
  $nav = "home";
  $helpPage = "opac";
  $focus_form_name = "phrasesearch";
  $focus_form_field = "searchText";
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE, $tab);

  $lookup = "N";
  if (isset($_GET["lookup"])) {
    $lookup = "Y";
    $helpPage = "opacLookup";
  }

  require_once("../opac/header_opac.php");
  print_r($_SESSION);
?>

<h3><?php echo $loc->getText("opac_Header");?></h3>

<form name="phrasesearch" method="POST" action="../shared/biblio_search.php">
  <div class="row">
    <div class="col-md-2">
        <select class="form-control" name="searchType">
          <option value="title" selected><?php echo $loc->getText("opac_Title");?>
          <option value="author"><?php echo $loc->getText("opac_Author");?>
          <option value="subject"><?php echo $loc->getText("opac_Subject");?>
          <option value="isbn"><?php echo $loc->getText("opac_ISBN");?>
          <option value="language"><?php echo $loc->getText("opac_Language"); ?></option>
        </select>
    </div>
    <div class="col-md-4">
      <div class="input-group">
        <input type="text" name="searchText" class="form-control">
        <input type="hidden" name="sortBy" value="default">
        <input type="hidden" name="tab" value="<?php echo H($tab); ?>">
        <input type="hidden" name="lookup" value="<?php echo H($lookup); ?>">
        <span class="input-group-btn">
          <input type="submit" value="<?php echo $loc->getText("opac_Search");?>" class="btn btn-primary">
        </span>
      </div>
    </div>
  </div>
</form>

<?php include("../shared/advanced_search.php") ?>
<?php include("../shared/footer.php"); ?>

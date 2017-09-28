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
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#main-content').fadeIn();
});
</script>

<div id="main-content">
  <div class="row" id="titulo" style="/*margin-top: 25%*/">
    <p><?php echo $loc->getText("opac_Header");?></p>
  </div>

  <form name="phrasesearch" method="POST" action="../shared/biblio_search.php">
    <div class="row row-centered" style="max-width: 600; margin-top: 25;">
      <div class="col-md-3">
          <select class="form-control" name="searchType">
            <option value="title" selected><?php echo $loc->getText("opac_Title");?>
            <option value="author"><?php echo $loc->getText("opac_Author");?>
            <option value="subject"><?php echo $loc->getText("opac_Subject");?>
            <option value="isbn"><?php echo $loc->getText("opac_ISBN");?>
            <!--<option value="language"><?php echo $loc->getText("opac_Language"); ?></option>-->
          </select>
      </div>
      <div class="col-md-9">
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

  <!--<?php include("../shared/advanced_search.php") ?>-->

  <div class="row" style="margin-top: 10%">
    <div class="col-md-6 col-centered">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Información de la biblioteca</h3>
        </div>
        <div class="nopadding panel-body">
           <table class="nomarginbottom nomargin table">
             <tr>
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_HOURS != "") echo $headerLoc->getText("headerLibraryHours");?></font></td>
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_HOURS != "") echo H(OBIB_LIBRARY_HOURS);?></font></td>
             </tr>
             <tr>
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_ADERS != "") echo $headerLoc->getText("headerLibraryAders");?></font></td>
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_ADERS != "") echo H(OBIB_LIBRARY_ADERS);?></font></td>
             </tr>
             <tr>
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_PHONE != "") echo $headerLoc->getText("headerLibraryPhone");?></font></td>
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_PHONE != "") echo H(OBIB_LIBRARY_PHONE);?></font></td>
             </tr>
           </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include("../shared/footer.php"); ?>

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

<link rel="stylesheet" href="../css/material/select/getmdl-select.min.css">
<script defer src="../css/material/select/getmdl-select.min.js"></script>
<link rel="stylesheet" href="../css/material/Material.min.css">
<script src="../css/material/material.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#main-content').fadeIn();
});
</script>

<div id="main-content">
  <div class="row" id="titulo" style="/*margin-top: 25%*/">
    <div><?php echo $loc->getText("opac_Header");?></div>
  </div>

  <form name="phrasesearch" method="POST" action="../shared/biblio_search.php">
    <div class="row row-centered" style="max-width: 600; margin-top: 25;">
      <div class="col-md-3">


    <div class="mdl-textfield mdl-js-textfield getmdl-select">
        <input type="text" value="" style="border-bottom: 1px solid rgb(255, 255, 255); color: white" class="mdl-textfield__input" id="searchType" readonly>
        <input type="hidden" value="" name="searchType">
        <i class="mdl-icon-toggle__label material-icons" style="color: white">keyboard_arrow_down</i>
        <!--<label for="sample2" class="mdl-textfield__label" style="color: white">Country</label>-->
        <ul for="searchType" class="mdl-menu mdl-menu--bottom-left mdl-js-menu" style="color: white">
            <li class="mdl-menu__item" data-val="title" data-selected="true"><?php echo $loc->getText("opac_Title");?></li>
            <li class="mdl-menu__item" data-val="author"><?php echo $loc->getText("opac_Author");?></li>
            <li class="mdl-menu__item" data-val="subject"><?php echo $loc->getText("opac_Subject");?></li>
            <li class="mdl-menu__item" data-val="isbn"><?php echo $loc->getText("opac_ISBN");?></li>
        </ul>
    </div>



      </div>

      <div class="col-md-9">


          <div class="mdl-textfield mdl-js-textfield" style="width: 380px">
            <input class="mdl-textfield__input" type="text" name="searchText" style="border-bottom: 1px solid rgb(255, 255, 255); color: #fff">
            <!--<label class="mdl-textfield__label" for="sample1"></label>-->
          </div>
          <input type="hidden" name="sortBy" value="default">
          <input type="hidden" name="tab" value="<?php echo H($tab); ?>">
          <input type="hidden" name="lookup" value="<?php echo H($lookup); ?>">
          <button class="mdl-button mdl-js-button mdl-button--icon" type="submit" style="color: white">
              <i class="material-icons">search</i>
          </button>

      </div>
    </div>
  </form>

  <!--<?php include("../shared/advanced_search.php") ?>-->

  <div class="row" style="margin-top: 10%">
    <div class="col-md-5 col-centered">
      <div class="mdl-card mdl-shadow--2dp" style="width: unset;">
        <div class="mdl-card__title">
          <h3 class="mdl-card__title-text">Información de la biblioteca</h3>
        </div>
        <div class="mdl-card__supporting-text">
           <table class="">
             <tr>
               <!--<td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_HOURS != "") echo $headerLoc->getText("headerLibraryHours");?></font></td>-->
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_HOURS != "") echo H(OBIB_LIBRARY_HOURS);?></font></td>
             </tr>
             <tr>
               <!--<td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_ADERS != "") echo $headerLoc->getText("headerLibraryAders");?></font></td>-->
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_ADERS != "") echo H(OBIB_LIBRARY_ADERS);?></font></td>
             </tr>
             <tr>
               <!--<td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_PHONE != "") echo $headerLoc->getText("headerLibraryPhone");?></font></td>-->
               <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_PHONE != "") echo H(OBIB_LIBRARY_PHONE);?></font></td>
             </tr>
           </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include("../shared/footer.php"); ?>

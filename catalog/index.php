<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "cataloging";
  $nav = "searchform";
  $helpPage = "cataloging";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "searchText";

  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
?>

<div class="container-fluid"> 

    <h1><img src="../images/catalog.png" border="0" width="30" height="30" align="top"> <?php echo $loc->getText("indexHdr");?></h1>

    
  <div class="row">

    <form name="barcodesearch" method="POST" action="../shared/biblio_search.php">
        <div class="form-group">

          <div>
            <label>
              <?php echo $loc->getText("indexBarcodeHdr");?>:
            </label>
          </div>

          <div class="col-lg-6">
            <!--
            <td nowrap="true" class="primary">
              <?php echo $loc->getText("indexBarcodeField");?>:
              <input type="text" name="searchText" size="20" maxlength="20">
              <input type="hidden" name="searchType" value="barcodeNmbr">
              <input type="hidden" name="sortBy" value="default">
              <input type="submit" value="<?php echo $loc->getText("indexButton");?>" class="button">
            </td>
            -->
            <div class="input-group">
              <input type="text" name="searchText" class="form-control" placeholder="<?php echo $loc->getText("indexBarcodeField");?>:">
              <input type="hidden" name="searchType" value="barcodeNmbr">
              <input type="hidden" name="sortBy" value="default">
              <span class="input-group-btn">
                <input class="btn btn-primary" type="submit" value="<?php echo $loc->getText("indexButton");?>">
                </input>
            </span>
            </div>

          </div>

      </div>
    </form>
  </div>




  <div class="row">

    <form name="phrasesearch" method="POST" action="../shared/biblio_search.php">
      <div class="form-group">
          <div>
            <label>
              <?php echo $loc->getText("indexSearchHdr");?>:
            </label>
          </div>

          <div class="col-lg-2">                  
                      <select class="form-control" name="searchType">
                        <option value="title" selected><?php echo $loc->getText("indexTitle");?>
                        <option value="author"><?php echo $loc->getText("indexAuthor");?>
                        <option value="subject"><?php echo $loc->getText("indexSubject");?>
                        <option value="isbn"><?php echo $loc->getText("indexISBN");?>
                        <!-- 
                        <option value="keyword"><?php echo $loc->getText("indexKeyword"); ?></option>
                        <option value="callno"><?php echo $loc->getText("indexCallNmbr"); ?></option>
                        <option value="collection"><?php echo $loc->getText("indexCollection"); ?></option>
                        <option value="series"><?php echo $loc->getText("indexSeries"); ?></option> 
                        <option value="publisher"><?php echo $loc->getText("indexPublisher"); ?></option>
                        <option value="id"><?php echo $loc->getText("indexId"); ?></option>
                        <option value="material"><?php echo $loc->getText("indexMaterial"); ?></option>
                 -->
                         <option value="language"><?php echo $loc->getText("Language"); ?></option>
                      </select>             
          </div>

          <div class="col-lg-4">
                    <div class="input-group">  
                      <input type="text" name="searchText" class="form-control">
                      <input type="hidden" name="sortBy" value="default">
                      <input type="hidden" name="tab" value="<?php echo H($tab); ?>"><!-- revisar -->
                      <input type="hidden" name="lookup" value="<?php echo H($lookup); ?>"><!-- revisar -->
                      <span class="input-group-btn">
                      <input class="btn btn-primary" type="submit" value="<?php echo $loc->getText("indexButton");?>" class="button">
                      </span>
                    </div>                    
            </div>

          </div>
        
      </div>
    </form>



    <?php include("../shared/advanced_search.php"); ?>

</div>

<?php include("../shared/footer.php"); ?>





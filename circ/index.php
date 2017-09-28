<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */ 
  require_once("../shared/common.php");
  session_cache_limiter(null);
  $tab = "circulation";
  $nav = "searchform";
  $helpPage = "circulation";
  $focus_form_name = "phrasesearch";
  $focus_form_field = "searchText";
  
  require_once("../shared/logincheck.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  
?>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script type="text/javascript">

  </script>

<div class="container-fluid">
  <h3><?php echo $loc->getText("indexHeading"); ?></h3>
  <hr/>  

  <div class="row">
    <form name="phrasesearch" method="POST" action="../circ/mbr_search.php">
      <div class="form-group">
        <div>
          <h5>  
            <?php echo $loc->getText("indexCardHdr"); ?> 
          </h5>
        </div> 

        <div class="col-lg-8">
          <div class="input-group">
            <input type="text" name="searchText" class="form-control" placeholder="Numero de tarjeta">
            <input type="hidden" name="searchType" value="barcodeNmbr">
            <span class="input-group-btn">
              <input class="btn btn-primary" type="submit" value="<?php echo $loc->getText("indexSearch"); ?>">
              </input>
            </span>
          </div>
        </div>

      </div>
    </form>
  </div>

  <div class="row">
    <form name="namesearch" method="POST" action="../circ/mbr_search.php">
      <div class="form-group">
        <div>
          <h5>
            <?php echo $loc->getText("indexNameHdr"); ?> 
          </h5>
        </div>

        <div class="col-lg-8">
          <div class="input-group">
            <input type="text" name="searchText" class="form-control" placeholder="Apellido">
            <input type="hidden" name="searchType" value="lastName">
            <span class="input-group-btn">
              <input class="btn btn-primary" type="submit" value="<?php echo $loc->getText("indexSearch"); ?>">
              </input>
            </span>
          </div>
        </div>

      </div>
    </form>
  </div>

</div>


<?php include("../shared/footer.php"); ?>


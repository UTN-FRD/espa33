<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  session_cache_limiter(null);
  header('Cache-Control: max-age=900');

  $tab = "circulation";
  $nav = "search";
  require_once("../shared/logincheck.php");
  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../functions/searchFuncs.php");
  require_once("../classes/DmQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Function declaration only used on this page.
  #****************************************************************************
 /* function printResultPages($currPage, $pageCount) {
    global $loc;
    $maxPg = 21;
    if ($currPage > 1) {
      echo "<a href=\"javascript:changePage(".H(addslashes($currPage-1)).")\">&laquo;".$loc->getText("mbrsearchprev")."</a> ";
    }
    for ($i = 1; $i <= $pageCount; $i++) {
      if ($i < $maxPg) {
        if ($i == $currPage) {
          echo "<b>".H($i)."</b> ";
        } else {
          echo "<a href=\"javascript:changePage(".H(addslashes($i)).")\">".H($i)."</a> ";
        }
      } elseif ($i == $maxPg) {
        echo "... ";
      }
    }
    if ($currPage < $pageCount) {
      echo "<a href=\"javascript:changePage(".($currPage+1).")\">".$loc->getText("mbrsearchnext")."&raquo;</a> ";
    }

 }
*/
    #****************************************************************************

    #****************************************************************************
    function printResultPages($currPage, $pageCount) {
      global $loc;
      if ($pageCount <= 1) {
      return false;
    }

      echo "<nav aria-label='Page navigation'><ul class='pagination'>";
     if ($currPage > 6) {
       echo "<li><a href=\"javascript:changePage(".H(addslashes(1)).")\">&laquo;".$loc->getText("First")."</a></li> ";
     }
      if ($currPage > 1) {
        echo "<li><a href=\"javascript:changePage(".H(addslashes($currPage-1)).")\">&laquo;".$loc->getText("mbrsearchprev")."</a></li> ";
      }
     $start = $currPage - 5;
     $end = $currPage + 5;
     if ($start<1) $start=1;
     if ($end>$pageCount) $end=$pageCount;
     for ($i = $start; $i <= $end; $i++) {
          if ($i == $currPage) {
            echo "<li class='active'><a href='#'>".H($i)."</a></li> ";
          } else {
            echo "<li><a href=\"javascript:changePage(".H(addslashes($i)).")\">".H($i)."</a></li> ";
          }
      }
      if ($currPage < $pageCount) {
        echo "<li><a href=\"javascript:changePage(".($currPage+1).")\">".$loc->getText("mbrsearchnext")."&raquo;</a></li> ";
      }
     if ($currPage < $pageCount - 5) {
       echo "<li><a href=\"javascript:changePage(".($pageCount).")\">".$loc->getText("Last")."&raquo;</a></li> ";
     }
     echo "</ul></nav>";
    }

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  $mbrClassifyDm = $dmQ->getAssoc("mbr_classify_dm");
  $dmQ->close();

  #****************************************************************************
  #*  Retrieving post vars and scrubbing the data
  #****************************************************************************
  if (isset($_POST["page"])) {
    $currentPageNmbr = $_POST["page"];
  } else {
    $currentPageNmbr = 1;
  }
  $searchType = $_POST["searchType"];
  $searchText = trim($_POST["searchText"]);
  # remove redundant whitespace
  $searchText = preg_replace("/[[:space:]]+/", " ", $searchText);

  if ($searchType == "barcodeNmbr") {
    $sType = OBIB_SEARCH_BARCODE;
  } else {
    $sType = OBIB_SEARCH_NAME;
  }

  #****************************************************************************
  #*  Search database
  #****************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->setItemsPerPage(OBIB_ITEMS_PER_PAGE);
  $mbrQ->connect();
  $mbrQ->execSearch($sType,$searchText,$currentPageNmbr);

  #**************************************************************************
  #*  Show member view screen if only one result from barcode query
  #**************************************************************************
  if (($sType == OBIB_SEARCH_BARCODE) && ($mbrQ->getRowCount() == 1)) {
    $mbr = $mbrQ->fetchMember();
    $mbrQ->close();
    header("Location: ../circ/mbr_view.php?mbrid=".U($mbr->getMbrid())."&reset=Y");
    exit();
  }

  #**************************************************************************
  #*  Show search results
  #**************************************************************************
  require_once("../shared/header.php");

  # Display no results message if no results returned from search.
  if ($mbrQ->getRowCount() == 0) {
    $mbrQ->close();
    echo '<div class="margin30 nomarginbottom alert alert-info">'.$loc->getText("mbrsearchNoResults").'</div>';
    require_once("../shared/footer.php");
    exit();
  }
?>

<!--**************************************************************************
    *  Javascript to post back to this page
    ************************************************************************** -->
<script language="JavaScript" type="text/javascript">
<!--
function changePage(page)
{
  document.changePageForm.page.value = page;
  document.changePageForm.submit();
}
-->
</script>


<!--**************************************************************************
    *  Form used by javascript to post back to this page
    ************************************************************************** -->
<form name="changePageForm" method="POST" action="../circ/mbr_search.php">
  <input type="hidden" name="searchType" value="<?php echo H($_POST["searchType"]);?>">
  <input type="hidden" name="searchText" value="<?php echo H($_POST["searchText"]);?>">
  <input type="hidden" name="page" value="1">
</form>

<!--**************************************************************************
    *  Printing result stats and page nav
    ************************************************************************** -->
<?php echo "<div class='alert alert-info'>".H($mbrQ->getRowCount()); echo $loc->getText("mbrsearchFoundResults")."</div>";?>
<?php printResultPages($currentPageNmbr, $mbrQ->getPageCount()); ?>
<h3>Resultados de la búsqueda</h3>

<!--**************************************************************************
    *  Printing result table
    ************************************************************************** -->

<div>
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Dirección</th>
        <th>Tarjeta</th>
        <th>Carrera</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php
        while ($mbr = $mbrQ->fetchMember()) {
      ?>

      <tr>
        <td>
          <a href="../circ/mbr_view.php?mbrid=<?php echo HURL($mbr->getMbrid());?>&amp;reset=Y"><?php echo H($mbr->getLastName());?>, <?php echo H($mbr->getFirstName());?></a>
        </td>
        <td>
          <?php
            if ($mbr->getAddress() != "")
              echo  H($mbr->getAddress());
            if ($mbr->getCity() != "")
              echo  " - ".H($mbr->getCity());
          ?>
        </td>
        <td>
          <?php echo H($mbr->getBarcodeNmbr());?>
        </td>
        <td>
          <?php echo H($mbrClassifyDm[$mbr->getClassification()]);?>
        </td>
        <td>
          <?php
            if (strcmp($mbr->getStatus(), "Y") == 0) { 
              echo $loc->getText("mbrActive");
            } elseif (strcmp($mbr->getStatus(), "N") == 0) { 
              echo $loc->getText("mbrInactive");
            }
          ?>
        </td>
      </tr>

      <?php
        }
        $mbrQ->close();
      ?>
    </tbody>
  </table>
</div>


<!-- Tabla vieja
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left" colspan="2">
      <?php echo $loc->getText("mbrsearchSearchResults");?>
    </th>
  </tr>
  <?php
    while ($mbr = $mbrQ->fetchMember()) {
  ?>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo H($mbrQ->getCurrentRowNmbr());?>.
    </td>
    <td nowrap="true" class="primary">
      <a href="../circ/mbr_view.php?mbrid=<?php echo HURL($mbr->getMbrid());?>&amp;reset=Y"><?php echo H($mbr->getLastName());?>, <?php echo H($mbr->getFirstName());?></a><br>
      <?php
        if ($mbr->getAddress() != "")
          echo str_replace("\n", "<br />", H($mbr->getAddress()));
        if ($mbr->getCity() != "")
          echo  " - ".H($mbr->getCity());
        echo "<br />";
      ?>
      <b><?php echo $loc->getText("mbrsearchCardNumber");?></b> <?php echo H($mbr->getBarcodeNmbr());?><br />
      <b><?php echo $loc->getText("mbrsearchClassification");?></b> <?php echo H($mbrClassifyDm[$mbr->getClassification()]);?><br />
      <b><?php echo $loc->getText("mbrsearchStatus");?></b>
      <?php
        if (strcmp($mbr->getStatus(), "Y") == 0) { 
          echo $loc->getText("mbrActive");
        } elseif (strcmp($mbr->getStatus(), "N") == 0) { 
          echo $loc->getText("mbrInactive");
        }
      ?>
    </td>
  </tr>


  <?php
    }
    $mbrQ->close();
  ?>
  </table><br>

-->

<?php printResultPages($currentPageNmbr, $mbrQ->getPageCount()); ?><br>
<?php require_once("../shared/footer.php"); ?>

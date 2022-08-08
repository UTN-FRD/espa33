<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
require_once("../shared/common.php");
header('Cache-Control: no cache');
session_cache_limiter('private_no_expire');

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0 && empty($_GET['tag'])) {
    header("Location: ../catalog/index.php");
    exit();
  }

  #****************************************************************************
  #*  Checking for tab name to show OPAC look and feel if searching from OPAC
  #****************************************************************************
  $tab = "cataloging";
  $helpPage = "biblioSearch";
  $lookup = "N";
//jalg modificado para busquedas aisladas de autores entre administrador y opac 10/jul/2013
  if (isset($_POST["tab"]) || isset($_GET['tab'])) {
    if (isset($_POST["tab"])) {
      $tab = $_POST["tab"];
    }
    if (isset($_GET["tab"])) {
      $tab = $_GET["tab"];
    }
  }
//jalg modificado para busquedas aisladas de autores entre administrador y opac 10/jul/2013
  if (isset($_POST["lookup"])) {
    $lookup = $_POST["lookup"];
    if ($lookup == 'Y') {
      $helpPage = "opacLookup";
    }
  }

  $nav = "search";

  if ($tab != "opac") {
    require_once("../shared/logincheck.php");
  }
  require_once("../classes/BiblioSearch.php");
  require_once("../classes/BiblioSearchQuery.php");
  require_once("../classes/BiblioFieldQuery.php");
  require_once("../functions/searchFuncs.php");
  require_once("../functions/cleanString.php"); //Añadida por Bruj0 para limpiar cadenas con caracteres extraños, al parecer no se usa 27jun2014
  require_once("../classes/DmQuery.php");

  #****************************************************************************
  #*  Function declaration only used on this page.
  #****************************************************************************
  function printResultPages(&$loc, $currPage, $pageCount, $sort) {
    if ($pageCount <= 1) {
      return false;
    }

      echo "<nav aria-label='Page navigation'><ul class='pagination'>";
     if ($currPage > 6) {
       echo "<li><a href=\"javascript:changePage(".H(addslashes(1)).",'".H(addslashes($sort))."')\">&laquo;".$loc->getText("First")."</a></li>";
     }
      if ($currPage > 1) {
        echo "<li><a href=\"javascript:changePage(".H(addslashes($currPage-1)).",'".H(addslashes($sort))."')\">&laquo;".$loc->getText("biblioSearchPrev")."</a></li> ";
   //     echo "<a href=\"javascript:changePage(".$pageCount.",'".$sort."')\">".$loc->getText("biblioSearchLast")."&raquo;</a> ";
      }
     $start = $currPage - 5;
     $end = $currPage + 5;
     if ($start<1) $start=1;
     if ($end>$pageCount) $end=$pageCount;
     for ($i = $start; $i <= $end; $i++) {
          if ($i == $currPage) {
            echo "<li class='active'><a href='#'>".H($i)."</a></li>";
          } else {
            echo "<li><a href=\"javascript:changePage(".H(addslashes($i)).",'".H(addslashes($sort))."')\">".H($i)."</a></li>";
          }
      }
      if ($currPage < $pageCount) {
        echo "<li><a href=\"javascript:changePage(".($currPage+1).",'".$sort."')\">".$loc->getText("biblioSearchNext")."&raquo;</a></li>";
         
      }
     if ($currPage < $pageCount-5) {
       echo "<li><a href=\"javascript:changePage(".H(addslashes($pageCount)).",'".H(addslashes($sort))."')\">".$loc->getText("Last")."&raquo;</a></li>";
     }
     echo "</ul></nav>";
    }


  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  $collectionDm = $dmQ->getAssoc("collection_dm");
  $materialTypeDm = $dmQ->getAssoc("material_type_dm");
  $materialImageFiles = $dmQ->getAssoc("material_type_dm", "image_file");
  $biblioStatusDm = $dmQ->getAssoc("biblio_status_dm");
  $dmQ->close();

  #****************************************************************************
  #*  Retrieving post vars and scrubbing the data
  #****************************************************************************
  if (isset($_POST["page"])) {
    $currentPageNmbr = $_POST["page"];
  } else {
    $currentPageNmbr = 1;
  }
  
  if (!empty($_POST['searchType'])) {
    $searchType = $_POST["searchType"];
    $sortBy = $_POST["sortBy"];
    if ($sortBy == "default") {
      if ($searchType == "author") {
        $sortBy = "author";
      } else {
        $sortBy = "title";
      }
    }
    $searchText = trim($_POST["searchText"]);
    # remove redundant whitespace
    $searchText = preg_replace("/[[:space:]]+/i", " ", $searchText);
    if ($searchType == "barcodeNmbr") {
      $sType = OBIB_SEARCH_BARCODE;
      $words[] = $searchText;
    } else {
      if ($searchType == "rfid"){
        $sType = OBIB_SEARCH_RFID;
        $words[] = trim($searchText);
      }
      else {
        $words = explodeQuoted($searchText);
        if ($searchType == "author") {
         $sType = OBIB_SEARCH_AUTHOR;
        } elseif ($searchType == "subject") {
         $sType = OBIB_SEARCH_SUBJECT;
        } elseif ($searchType == "isbn") {
          $sType = OBIB_SEARCH_ISBN;
// añadido de filtros busquedas 3-2015 JALG
        } elseif ($searchType == "language") {
          $sType = OBIB_SEARCH_LANGUAGE;
        } elseif ($searchType == "material") {        
          $sType = OBIB_SEARCH_MATERIAL;
// añadido de filtros busquedas 3-2015 JALG
        } elseif ($searchType == "advanced") {
          $sType = OBIB_ADVANCED_SEARCH;
          $words = $_POST;
        } else {
          $sType = OBIB_SEARCH_TITLE;
       }
     }
    }
  }
  else if (isset($_GET['tag'])) {
    $sortBy = 'title';
    $words = $_GET['words'];
    if (empty($words)) {
      $words = '';
    }
  }

  #****************************************************************************
  #*  Search database
  #****************************************************************************
  $biblioQ = new BiblioSearchQuery();
  $biblioQ->setItemsPerPage(OBIB_ITEMS_PER_PAGE);
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  # checking to see if we are in the opac search or logged in
  if ($tab == "opac") {
    $opacFlg = true;
  } else {
    $opacFlg = false;
  }
  if (!empty($searchType)) {
    if (!$biblioQ->search($sType, $words, $currentPageNmbr, $sortBy, $opacFlg)) {
      $biblioQ->close();
      displayErrorPage($biblioQ);
    }
  }  else if (isset($_GET['tag'])) {
    if (!$biblioQ->searchTag($_GET['tag'], $words, $currentPageNmbr, $opacFlg)) {
      $biblioQ->close();
      displayErrorPage($biblioQ);
    }
  }  else {
    exit();
  }

  #**************************************************************************
  #*  Show search results
  #**************************************************************************
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,"shared");

  # Display no results message if no results returned from search.
  if ($biblioQ->getRowCount() == 0) {
    if ($tab == "opac") {
        require_once("../opac/header_opac.php");
      } else {
        require_once("../shared/header.php");
      }
    $biblioQ->close();
    ?> <h3> <?php echo $loc->getText("biblioSearchNoResults"); ?> </h3> <?php
    require_once("../shared/footer.php");
    exit();
  }
  if ($biblioQ->getRowCount() == 1) {
    $biblio = $biblioQ->fetchRow();
    $bibid = $biblio->getBibid();
    $biblioQ->close();
    header('Location: ../shared/biblio_view.php?bibid='.$bibid.'&tab='.$tab);
    exit();
  }
  if ($tab == "opac") {
    require_once("../opac/header_opac.php");
  } else {
    require_once("../shared/header.php");
  }
?>

<!--**************************************************************************
    *  Javascript to post back to this page
    ************************************************************************** -->
<script language="JavaScript" type="text/javascript">
<!--
function changePage(page,sort)
{
  document.changePageForm.page.value = page;
  document.changePageForm.sortBy.value = sort;
  document.changePageForm.submit();
}
-->
</script>

<!--**************************************************************************
    *  Form used by javascript to post back to this page
    ************************************************************************** -->
      
<form name="changePageForm" method="POST" action="../shared/biblio_search.php<?php echo isset($_REQUEST['tag']) ? '?tag=' . $_REQUEST['tag'] . '&words=' . $_REQUEST['words'] : ''?>">
  <input type="hidden" name="searchType"    value="<?php echo H($_REQUEST["searchType"]);?>">
  <input type="hidden" name="searchText"    value="<?php echo H($_REQUEST["searchText"]);?>">
  <input type="hidden" name="keyword_type_1"  value="<?php echo H($_REQUEST["keyword_type_1"]);?>">
  <input type="hidden" name="keyword_text_1"  value="<?php echo H($_REQUEST["keyword_text_1"]);?>">  
  <input type="hidden" name="expression_2"  value="<?php echo H($_REQUEST["expression_2"]);?>">
  <input type="hidden" name="keyword_type_2"  value="<?php echo H($_REQUEST["keyword_type_2"]);?>">  
  <input type="hidden" name="keyword_text_2"  value="<?php echo H($_REQUEST["keyword_text_2"]);?>">
  <input type="hidden" name="publishedYear" value="<?php echo H($_REQUEST["publishedYear"]);?>">
  <input type="hidden" name="language"      value="<?php echo H($_REQUEST["language"]);?>">
  <input type="hidden" name="materialCd"    value="<?php echo H($_REQUEST["materialCd"]);?>">
  <input type="hidden" name="collectionCd"  value="<?php echo H($_REQUEST["collectionCd"]);?>">  
<!--   <input type="hidden" name="lookup"   value="<?php echo H($_REQUEST["lookup"]);?>">  -->
  <input type="hidden" name="sortBy"      value="<?php echo H($_REQUEST["sortBy"]);?>">
  <input type="hidden" name="lookup"      value="<?php echo H($lookup);?>">
  <input type="hidden" name="page"        value="1">
  <input type="hidden" name="tab"       value="<?php echo H($tab);?>">
</form>

<!--**************************************************************************
    *  Printing result stats and page nav
    ************************************************************************** -->
<?php echo "<div class='alert alert-info' style='margin-bottom: 0px'>".$biblioQ->getRowCount()." resultados encontrados para <b>".H($_REQUEST["searchType"])."</b> igual a <b>".H($_REQUEST["searchText"])."</b></div>";?>
<?php  printResultPages($loc, $currentPageNmbr, $biblioQ->getPageCount(), $sortBy); ?>
<h3><?php echo $loc->getText("biblioSearchResults"); ?></h3>

<!--**************************************************************************
    *  Printing result table
    ************************************************************************** -->
<table class="table">
  <tr>
    <th>#</th>
    <th>T&iacute;tulo</th>
    <th>Autor</th>
    <th>C&oacute;digo</th>
    <th>Copias</th>
  </tr>
  <?php
    // Show bibliographies
    while ($biblio = $biblioQ->fetchRow()) { // START WHILE 1

  ?>
<?php
  #****************************************************************************
  #*  Muestra imagenes del tipo d ematerial
  #****************************************************************************  ?>
  <tr>
    <td align="center">
      <?php echo H($biblioQ->getCurrentRowNmbr());?>
    </td>
  
<?php 

  #****************************************************************************
  #*  Retrieving get var
  #*  modificado por lara para mostrar portadas de libro.
  #****************************************************************************
  $bibid = $biblio->getBibid();
  require_once("../classes/UsmarcTagDm.php");
  require_once("../classes/UsmarcTagDmQuery.php");
  require_once("../classes/UsmarcSubfieldDm.php");
  require_once("../classes/UsmarcSubfieldDmQuery.php");
  require_once("../functions/marcFuncs.php");
  require_once("../classes/BiblioFieldQuery.php");
  require_once("../classes/BiblioQuery.php");

//  $loc = new Localize(OBIB_LOCALE,"shared");

  if (isset($_GET["msg"])) {
    $msg = "<font class=\"error\">".H($_GET["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays marc
  #****************************************************************************
  $LdmQ = new DmQuery();
  $LdmQ->connect();
  $collectionDm = $LdmQ->getAssoc("collection_dm");
  $LmaterialTypeDm = $LdmQ->getAssoc("material_type_dm");
  $LbiblioStatusDm = $LdmQ->getAssoc("biblio_status_dm");
  $LdmQ->close();

  $LmarcTagDmQ = new UsmarcTagDmQuery();
  $LmarcTagDmQ->connect();
  if ($LmarcTagDmQ->errorOccurred()) {
    $LmarcTagDmQ->close();
    displayErrorPage($LmarcTagDmQ);
  }
  $LmarcTagDmQ->execSelect();
  if ($LmarcTagDmQ->errorOccurred()) {
    $LmarcTagDmQ->close();
    displayErrorPage($marcTagDmQ);
  }
  $LmarcTags = $LmarcTagDmQ->fetchRows();
  $LmarcTagDmQ->close();

  $LmarcSubfldDmQ = new UsmarcSubfieldDmQuery();
  $LmarcSubfldDmQ->connect();
  if ($LmarcSubfldDmQ->errorOccurred()) {
    $LmarcSubfldDmQ->close();
    displayErrorPage($marcSubfldDmQ);
  }
  $LmarcSubfldDmQ->execSelect();
  if ($LmarcSubfldDmQ->errorOccurred()) {
    $LmarcSubfldDmQ->close();
    displayErrorPage($LmarcSubfldDmQ);
  }
  $LmarcSubflds = $LmarcSubfldDmQ->fetchRows();
  $LmarcSubfldDmQ->close();

  #****************************************************************************
  #*  Search database marc
  #****************************************************************************

  $LbiblioQ = new BiblioQuery();
  $LbiblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($LbiblioQ);
  }
  if (!$Lbiblio = $LbiblioQ->doQuery($bibid)) {
    $LbiblioQ->close();
    displayErrorPage($LbiblioQ);
  }
  $LbiblioFlds = $Lbiblio->getBiblioFields();
// hasta aqui la modificacion para la busqueda de valores marc, ya que la portada se guarda en marc 902a
  ?>
   
<!--**************************************************************************
    *  Mostrar  titulos joanlga@hotmail.com 
    ************************************************************************** -->
    <td>
       <a href="../shared/biblio_view.php?bibid=<?php echo HURL($biblio->getBibid());?>&amp;tab=<?php echo HURL($tab);?>"><?php echo  substr(H($biblio->getTitle()),0,75);?></a>
    </td>
    <td>
      <?php 
          if ($biblio->getAuthor() != "") {
            $val = H($biblio->getAuthor());
//jalg modificado para busquedas aisladas de autores entre administrador y opac 10/jul/2013
            echo '<a href="../shared/biblio_search.php?tag=100a&words=' . $val . '&amp;tab=' . HURL($tab) . '">' . $val . '</a>';
//jalg modificado para busquedas aisladas de autores entre administrador y opac 10/jul/2013
          }
          ?>
    </td>
    <td>
      <?php echo H($biblio->getCallNmbr1()." ".$biblio->getCallNmbr2()." ".$biblio->getCallNmbr3());?>
    </td>
<!--**************************************************************************
    // Show bibliography copies
    *   Modificado por jose lara joanlaga@hotmail.com para mostrar foto de portada cuando no es capturada
    ************************************************************************** -->
    
  <?php
    require_once('../classes/BiblioCopyQuery.php');
    $copyQ = new BiblioCopyQuery();
    $copyQ->connect();
    if ($copyQ->errorOccurred()) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
    if (!$copy = $copyQ->execSelect($biblio->getBibid())) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
  ?>

<td>
<?php echo $copyQ->getRowCount() ?>
</td>
  </tr>

  <?php
    if ($copyQ->getRowCount() == 0) { // START IFELSE 2
  ?>
  <?php
    } // END IFELSE 2

    } // END WHILE 1
    $biblioQ->close();
  ?>
  </table><br>

<?php printResultPages($loc, $currentPageNmbr, $biblioQ->getPageCount(), $sortBy); ?><br>

<?php require_once("../shared/footer.php"); ?>

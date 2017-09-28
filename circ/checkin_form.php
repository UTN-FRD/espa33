<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "checkin";
  $helpPage = "checkin";
  $focus_form_name = "barcodesearch";
  $focus_form_field = "barcodeNmbr";

/*  function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
  } 
  $startTm = getmicrotime();
*/

/*
  $endTm = getmicrotime();
  trigger_error ("read_settings: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
  $startTm = getmicrotime();
*/

  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioSearch.php");
  require_once("../classes/BiblioSearchQuery.php");
  require_once("../classes/MemberQuery.php");
  require_once("../shared/get_form_vars.php");
  require_once("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

/*
  $endTm = getmicrotime();
  trigger_error ("Header: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
  $startTm = getmicrotime();
*/
?>


<!--**************************************************************************
    *  Javascript to post checkin form
    ************************************************************************** -->
<script language="JavaScript" type="text/javascript">
<!--
function checkin(massCheckinFlg)
{
  document.checkinForm.massCheckin.value = massCheckinFlg;
  document.checkinForm.submit();
}
-->
</script>

<?php
  if (isset($_GET['barcode'])) {
    if (isset($_GET['mbrid']) and $_GET['mbrid']) {
      $memberQ = new MemberQuery;
      $mbr = $memberQ->get($_GET['mbrid']);
      echo '<div class="margin30 nomarginbottom alert alert-success">';
      echo $loc->getText("checkinDone1", array(
        'barcode'=>$_GET['barcode'],
        'fname'=>$mbr->getFirstName(),
        'lname'=>$mbr->getLastName(),
      ));
      echo '</div>';
    } else {
      echo '<div class="margin30 nomarginbottom alert alert-success">'.$loc->getText("checkinDone2", array('barcode'=>$_GET['barcode'])).'</div>';
    }
  }
  if (isset($_SESSION['feeMsg'])) {
    echo '<div class="margin30 nomarginbottom alert alert-danger">'.$_SESSION['feeMsg'].'</div>';
    unset($_SESSION['feeMsg']);
  }
?>

<div class="container-fluid">
<form name="barcodesearch" method="POST" action="../circ/shelving_cart.php">
  <div class="margin30 row">
    <div class="col col-md-6">
      <div class="col col-md-3" style="min-width: 185px;">                  
        <select class="form-control" name="searchType">
          <option value="rfid" selected>Código RFID
          <option value="barcode">Número de copia</option>
        </select>             
      </div>
        <div class="input-group">
          <!--<?php echo $loc->getText("checkinFormBarcode"); ?>-->
          <?php printInputText("barcodeNmbr",18,40,$postVars,$pageErrors); ?>
          <!--<a class="btn btn-primary" href="javascript:popSecondaryLarge('../opac/index.php?lookup=Y')"><?php echo $loc->getText("indexSearch"); ?></a>-->
          <input type="hidden" name="mbrid" value="<?php echo H($mbrid);?>">
          <span class="input-group-btn">
            <input id="submit" class="btn btn-primary" type="submit" value="Añadir" class="button">
          </span>
        </div>

        <script>
          $('#barcodeNmbr').attr('placeholder','Número');
        </script>
      
    </div>
  </div>
</form>

<?php
  if (isset($_GET["msg"])){
    echo "<div class=\"margin30 nomarginbottom alert alert-success\">";
    echo H($_GET["msg"])."</div>";
  }
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
   // On refresh check if there are values selected
    if (localStorage.selectVal) {
    // Select the value stored
    $('select').val( localStorage.selectVal );
    }
  });

  // On change store the value
  $('select').on('change', function(){
    var currentVal = $(this).val();
    localStorage.setItem('selectVal', currentVal );
  });
</script>

<form name="checkinForm" method="POST" action="../circ/checkin.php">
<input type="hidden" name="massCheckin" value="N">

<!--
<a href="javascript:checkin('N')"><?php echo $loc->getText("checkinFormCheckinLink1"); ?></a> | 
<a href="javascript:checkin('Y')"><?php echo $loc->getText("checkinFormCheckinLink2"); ?></a><br><br>
-->
      <h3><?php echo $loc->getText("checkinFormHdr2"); ?></h3>

<table class="table60 nomargin table">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      &nbsp;
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php echo $loc->getText("checkinFormColHdr4"); ?>
    </th>
  </tr>

<?php
  #****************************************************************************
  #*  Search database for biblio copy data
  #****************************************************************************
  $biblioQ = new BiblioSearchQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->doQuery(OBIB_STATUS_SHELVING_CART)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if ($biblioQ->getRowCount() == 0) {
?>
    <td class="primary" align="center" colspan="5">
      <?php echo $loc->getText("checkinFormEmptyCart"); ?>
    </td>
<?php
  } else {
    while ($biblio = $biblioQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" align="center">
      <input type="checkbox" name="bibid=<?php echo HURL($biblio->getBibid());?>&amp;copyid=<?php echo HURL($biblio->getCopyid());?>" value="copyid">
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo  date('d/m/y H:i',strtotime(($biblio->getStatusBeginDt())));?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getBarcodeNmbr());?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getTitle());?>
    </td>
    <td class="primary" valign="top" >
      <?php echo H($biblio->getAuthor());?>
    </td>
  </tr>
<?php
    }
  }
  $biblioQ->close();
?>
</table>
<br>
<a class="btn btn-primary" href="javascript:checkin('N')"><?php echo $loc->getText("checkinFormCheckinLink1"); ?></a> 
<a class="btn btn-primary" href="javascript:checkin('Y')"><?php echo $loc->getText("checkinFormCheckinLink2"); ?></a>
</form>

<script type="text/javascript">
  $('#barcodeNmbr').keyup(function () {
    if (this.value.length == 25) {
      $('#submit').click();
    }
  });

  $('#close').on("click", function(){
    $('#barcodeNmbr').val('');
    $('#barcodeNmbr').focus();
  });
</script>

</div>

<?php require_once("../shared/footer.php");

/*
  $endTm = getmicrotime();
  trigger_error ("Footer: start=".$startTm." end=".$endTm." diff=".($endTm - $startTm),E_USER_NOTICE);
*/
 ?>

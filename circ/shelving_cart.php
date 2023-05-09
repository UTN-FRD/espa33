<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "checkin";
  $restrictInDemo = true;
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/BiblioStatusHist.php");
  require_once("../classes/BiblioStatusHistQuery.php");
  require_once("../classes/MemberAccountTransaction.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../classes/MemberQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

 if (count($_POST) == 0  && count($_GET) == 0) {
    header("Location: ../circ/checkin_form.php?reset=Y");
    exit();
  }
  if (array_key_exists('barcodeNmbr', $_GET)) {
          $barcode = trim($_GET["barcodeNmbr"]);
  } else {
          $barcode = trim($_POST["barcodeNmbr"]);
  }

  if (array_key_exists('searchType', $_POST)) {
    $searchType = trim($_POST["searchType"]);
  } else {
    $searchType = null;
  }
  if ($searchType == "barcode" and !array_key_exists('barcodeNmbr', $_GET)) {
    $barcode = trim($_POST["barcodeNmbr"]);
  }
  if ($searchType == "rfid" and !array_key_exists('barcodeNmbr', $_GET)) {
    $rfid = trim($_POST["barcodeNmbr"]);
  }

  if (isset($_GET["from"])) {
    $from = $_GET["from"];
  } else {
    $from = "";
  }
   
  #****************************************************************************
  #*  Edit input
  #****************************************************************************
  if (!ctypeAlnum($barcode) and !ctypeAlnum($rfid)) {
    $pageErrors["barcodeNmbr"] = $loc->getText("shelvingCartErr1");
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/checkin_form.php");
    exit();
  }
  
  #****************************************************************************
  #*  Ready copy record
  #****************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }

  if ($searchType == "barcode" or $_GET["barcodeNmbr"]) {
    if (is_bool($copy = $copyQ->queryByBarcode($barcode))) {
      $copyQ->close();
      $pageErrors["barcodeNmbr"] = $loc->getText("shelvingCartErr2");
      $_SESSION["pageErrors"] = $pageErrors;
      header("Location: ../circ/checkin_form.php");
      exit();
    }
  } else {
    if (is_bool($copy = $copyQ->queryByRfid($rfid))) {
      $copyQ->close();
      $pageErrors["barcodeNmbr"] = $loc->getText("shelvingCartErr2");
      $_SESSION["pageErrors"] = $pageErrors;
      header("Location: ../circ/checkin_form.php");
      exit();
    }
  }

  #****************************************************************************
  #*  Edit results
  #****************************************************************************
  $foundError = FALSE;
  if ($copyQ->getRowCount() == 0) {
    $foundError = true;
    $pageErrors["barcodeNmbr"] = $loc->getText("shelvingCartErr2");
  }
  
  if ($copy->getStatusCd() != OBIB_STATUS_OUT) {
    $foundError = true;
    $pageErrors["barcodeNmbr"] = $loc->getText("shelvingCartErr3");
  }

  if ($foundError == true) {
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../circ/checkin_form.php");
    exit();
  }

  #****************************************************************************
  #*  Get daily late fee
  #****************************************************************************
  $dailyLateFee = $copyQ->getDailyLateFee($copy);
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }

  $copyQ->close();
  $saveMbrid = $copy->getMbrid();
  $saveDaysLate = $copy->getDaysLate();

  #**************************************************************************
  #*  Check hold list to see if someone has the copy on hold
  #**************************************************************************
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $hold = $holdQ->getFirstHold($copy->getBibid(),$copy->getCopyid());
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $holdQ->close();

  #**************************************************************************
  #*  Update copy status code
  #**************************************************************************
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if ($holdQ->getRowCount() > 0) {
    $copy->setStatusCd(OBIB_STATUS_ON_HOLD);
  } else {
    $copy->setStatusCd(OBIB_STATUS_SHELVING_CART);
  }
  $copy->setMbrid("");
  $copy->setDueBackDt("");
  $copy->setLastRenewalBy("");
  if (!$copyQ->update($copy,true)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->close();

  #**************************************************************************
  #*  Insert into biblio status history
  #**************************************************************************
  if ($saveMbrid != "") {
    $hist = new BiblioStatusHist();
    $hist->setBibid($copy->getBibid());
    $hist->setCopyid($copy->getCopyid());
    $hist->setStatusCd($copy->getStatusCd());
    $hist->setDueBackDt($copy->getDueBackDt());
    $hist->setMbrid($saveMbrid);

    $histQ = new BiblioStatusHistQuery();
    $histQ->connect();
    if ($histQ->errorOccurred()) {
      $histQ->close();
      displayErrorPage($histQ);
    }
    $histQ->insert($hist);
    if ($histQ->errorOccurred()) {
      $histQ->close();
      displayErrorPage($histQ);
    }
    $histQ->close();

    #**************************************************************************
    #*  Calc late fee if any
    #**************************************************************************
    if (($saveDaysLate > 0) and ($dailyLateFee > 0)) {
      $fee = $dailyLateFee * $saveDaysLate;
      $trans = new MemberAccountTransaction();
      $trans->setMbrid($saveMbrid);
      $trans->setCreateUserid($_SESSION["userid"]);
      $trans->setTransactionTypeCd("+c");
      $trans->setAmount($fee);
      $trans->setDescription($loc->getText("shelvingCartTrans",array("barcode" => $barcode)));

      $transQ = new MemberAccountQuery();
      $transQ->connect();
      if ($transQ->errorOccurred()) {
        $transQ->close();
        displayErrorPage($transQ);
      }
      $trans = $transQ->insert($trans);
      if ($transQ->errorOccurred()) {
        $transQ->close();
        displayErrorPage($transQ);
      }
      
      // Set fee message
      if (OBIB_LOCALE == 'th') {
        $balText = number_format($fee, 2) . ' บาท';
      }
      else {
        $balText = moneyFormat($fee,2);
      }
      $_SESSION['feeMsg'] = $loc->getText("mbrViewBalMsg2",array("fee"=>$balText))." <a href=\"../circ/mbr_account.php?mbrid=" . $saveMbrid . "&reset=Y\">" . 'Ver cuenta' . "</a>";
      
      $transQ->close();
    }
    
    // Update activity
    $mbrQ = new MemberQuery;
    $mbrQ->connect();
    $mbrQ->updateActivity($saveMbrid);
    $mbrQ->close();
  }

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  if ($holdQ->getRowCount() > 0) {
    // Si alguien tiene la copia reservada, mostramos el aviso
    // A hold_message.php le mandamos el mbrid para que luego pueda volver al usuario
    header("Location: ../circ/hold_message.php?barcode=".U($barcode)."&mbrid=".U($saveMbrid));
  } else {
    // Si no está reservada, redirigimos a la página anterior
    if ($from == "mbrview") {
      // Si viene del usuario, lo enviamos al usuario
      header("Location: ../circ/mbr_view.php?mbrid=".U($saveMbrid));
    } else {
      // Si viene de devoluciones, a devoluciones
      header("Location: ../circ/checkin_form.php?barcode=".U($barcode)."&mbrid=".U($saveMbrid));
    }
  }
?>
    

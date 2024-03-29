<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "user";
  $nav = "view";
  $restrictInDemo = true;
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once('../classes/MemberQuery.php');
  require_once("../functions/errorFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../user/index.php");
    exit();
  }
  $barcode = trim($_POST["holdBarcodeNmbr"]);
  $mbrid = trim($_POST["mbrid"]);

  #****************************************************************************
  #*  Edit input
  #****************************************************************************
  if (!ctypeAlnum($barcode)) {
    $pageErrors["holdBarcodeNmbr"] = $loc->getText("placeHoldErr1");
    $postVars["holdBarcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../user/user_view.php?mbrid=".U($mbrid));
    exit();
  }
  
  $mbrQ = new MemberQuery;
  $mbrQ->connect();
  $mbr = $mbrQ->get($mbrid);
  if (strcmp($mbr->getStatus(), "N") == 0) {
    $foundError = TRUE;
    $pageErrors["holdBarcodeNmbr"] = $loc->getText("checkoutErr9");
    $postVars["holdBarcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../user/user_view.php?mbrid=".U($mbrid));
    exit();
  }

  // Check to see if this member already has the item checked out.
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copy = $copyQ->queryByBarcode($barcode);
//echo "<pre> lara";
//print_r ($copy);
//echo "<pre>";
  if (!$copy) {
    $copyQ->close();
    displayErrorPage($copyQ);
  } else if (!is_a($copy, 'BiblioCopy')) {
    $pageErrors["holdBarcodeNmbr"] = $loc->getText("placeHoldErr2");
    $postVars["holdBarcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../user/user_view.php?mbrid=".U($mbrid));
    exit();
  } else if ($copy->getStatusCd() == OBIB_STATUS_OUT
             and $copy->getMbrid() == $mbrid) {
    $pageErrors["holdBarcodeNmbr"] = $loc->getText("placeHoldErr3");
    $postVars["holdBarcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../user/mbr_view.php?mbrid=".U($mbrid));
    exit();
  } else if ($copy->getStatusCd() != OBIB_STATUS_OUT) {
    $pageErrors["holdBarcodeNmbr"] = $loc->getText("placeHoldErrNotChkOut");
    $postVars["holdBarcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../user/user_view.php?mbrid=".U($mbrid));
    exit();
  }

  #**************************************************************************
  #*  Insert hold
  #**************************************************************************
  // we need to also insert into status history table
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  // Check existing holds, prevent member request to hold same book twice or more.
  $holdQ->queryByMbrid($mbrid);
  
  $duplicated = false;
  while ($hold = $holdQ->fetchRow()) {
    if ($hold->getBarcodeNmbr() == $barcode) {
      $pageErrors["holdBarcodeNmbr"] = $loc->getText("placeHoldErrDup");
      $postVars["holdBarcodeNmbr"] = $barcode;
      $_SESSION["postVars"] = $postVars;
      $_SESSION["pageErrors"] = $pageErrors;
      header("Location: ../user/user_view.php?mbrid=".U($mbrid));
      exit();
    }
  }

  $rc = $holdQ->insert($mbrid,$barcode);
  if (!$rc) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $holdQ->close();
  
  $mbrQ = new MemberQuery;
  $mbrQ->connect();
  $mbrQ->updateActivity($mbrid);
  $mbrQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  header("Location: ../user/user_view.php?mbrid=".U($_POST["mbrid"]));
?>

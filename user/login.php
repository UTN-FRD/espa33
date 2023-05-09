<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../classes/SessionQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  $pageErrors = array();
  if (count($_POST) == 0) {
   header("Location: ../user/index.php");
    exit();
  }

  #****************************************************************************
  #*  Username edits
  #****************************************************************************
  // Cambio de barcode_nmbr a username para que en navegador ofrezca guardar la clave
  if (!isset($_POST["barcode_nmbr"]) && isset($_POST["username"])) {
    $_POST["barcode_nmbr"] = $_POST["username"];
  }
  $mbrid = $_POST["barcode_nmbr"];
  if ($mbrid == "") {
    $error_found = true;
    $pageErrors["barcode_nmbr"] = "barcode_nmbr is required.";
  }

  #****************************************************************************
  #*  Password edits
  #****************************************************************************
  $error_found = false;
  if (!isset($_POST["pass_user"]) && isset($_POST["password"])) {
    $_POST["pass_user"] = $_POST["password"];
  }
  $pass_user = $_POST["pass_user"];
   $barcode_nmbr  = $_POST["barcode_nmbr"]; //modificado jalg por qu eno pasa las bariables d emanera uatamica revisar como lo hace staff quisas por el varpost
  if ($pass_user == "") {
    $error_found = true;
    $pageErrors["pass_user"] = "Escriba usuario y contraseña";
  } else {
    $mbrQ = new MemberQuery();
    $mbrQ->connect();
    if ($mbrQ->errorOccurred()) {
      displayErrorPage($pass_user);
    }
   $mbrQ->verifySignon($barcode_nmbr, $pass_user);
    if ($mbrQ->errorOccurred()) {
      displayErrorPage($pass_user);
    }
    $mbr = $mbrQ->fetchMember();
    if ($mbr == false) {
      # invalid password.  Add one to login attempts.
      $error_found = true;
      $pageErrors["pass_user"] = "El usuario o contraseña no existen";
      if (!isset($_SESSION["loginAttempts"]) || ($_SESSION["loginAttempts"] == "")) {
       $sess_login_attempts = 1;
      } else {
        $sess_login_attempts = $_SESSION["loginAttempts"] + 1;
      }
      # Suspend userid if login attempts >= 3
      if ($sess_login_attempts >= 3) {
        $mbrQ->getStatus($mbr);
        $mbrQ->close();
        header("Location: suspended.php");
        exit();
      }
    }
    $mbrQ->close();
  }

  #****************************************************************************
  #*  Redirect back to form if error occured
  #****************************************************************************
 if ($error_found == true) {
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../user/index.php");
    exit();
  }

  #****************************************************************************
  #*  Redirect to suspended message if suspended
  #****************************************************************************
 /* if ($mbr->isSuspended()) {
    header("Location: ../shared/suspended.php");
    exit();
  }
*/
  #**************************************************************************
  #*  Insert new session row with random token
  #**************************************************************************

  $sessionQ = new SessionQuery();
  $sessionQ->connect();
  if ($sessionQ->errorOccurred()) {
    $sessionQ->close();
    displayErrorPage($sessionQ);
  }
  $token = $sessionQ->getToken($mbr->getMbrid());
  if ($token == false) {
    $sessionQ->close();
    displayErrorPage($sessionQ);
  }
  $sessionQ->close();

  #**************************************************************************
  #*  Destroy form values and errors and reset signon variables
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  $_SESSION["barcode_nmbr"] = $mbr->getBarcodeNmbr();
  $_SESSION["mbrid"] = $mbr->getMbrid();
  $_SESSION["token"] = $token;
  $_SESSION["loginAttempts"] = 0;
//  $_SESSION["hasAdminAuth"] = $mbr->hasAdminAuth();
//  $_SESSION["hasCircAuth"] = $mbr->hasCircAuth();
  $_SESSION["hasCircAuth"] = "N";  //Importante
  $_SESSION["hasCircMbrAuth"] = "Y";
//  $_SESSION["hasCircMbrAuth"] = $mbr->hasCircMbrAuth();
 // $_SESSION["hasCatalogAuth"] = $mbr->hasCatalogAuth();
//  $_SESSION["hasReportsAuth"] = $mbr->hasReportsAuth();

  #**************************************************************************
  #*  Redirect to return page
  #**************************************************************************
  $_SESSION["returnPage"] = "../user/user_view.php";
  header("Location: ".$_SESSION["returnPage"]);
  exit();
?>

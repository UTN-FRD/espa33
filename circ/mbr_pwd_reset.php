<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
   require_once("../shared/common.php");
  $tab = "circulation";
  $nav = "view";
  $helpPage = "memberView";

  $focus_form_name = "pass_userResetform";
  $focus_form_field = "pass_user";

 // $focus_form_name = "barcodesearch";
//  $focus_form_field = "barcodeNmbr";
 
  $restrictInDemo = true;
  require_once("../shared/logincheck.php");

  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");

  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  
  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../circ/mbr_view.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $mbrid = $_POST["mbrid"];
    
  #****************************************************************************
    $memberQ = new MemberQuery();
    $memberQ->connect();
    if ($memberQ->errorOccurred()) {
        $memberQ->close();
        displayErrorPage($memberQ);
      }
    $member = $memberQ->get($_POST["mbrid"]);
    $memberQ->close();

    $barcode = $_POST["barcode_nmbr"];
    $member->setPassUser($_POST["pass_user"]);
    $_POST["pass_user"] = $member->getPassUser();
    $member->setPassUser2($_POST["pass_user2"]);
    $_POST["pass_user2"] = $member->getPassUser2();
    if (!$member->validatePassUser()) {
        $pageErrors["pass_user"] = $member->getPassUserError();
        $_SESSION["postVars"] = $_POST;
        $_SESSION["pageErrors"] = $pageErrors;

        header("Location: ../circ/mbr_pwd_reset_form.php");
        exit();
    }

  #**************************************************************************
  #*  Update staff member
  #**************************************************************************
  if (!$memberQ->resetPassUser($member)) {
    $memberQ->close();
    displayErrorPage($memberQ);
  }
  $uidmember = $memberQ->maybeGetByBarcode($barcode);
  $memberQ->close();

  $uid = $uidmember->getMbrid();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************

  require_once("../shared/header.php");
?>

<h3><?php echo $loc->getText("adminStaff_pass_user_reset_Passwordreset"); ?></h3>
<a href="../circ/mbr_view.php?mbrid=<?php echo $uid; ?>"><?php echo $loc->getText("adminStaff_Return"); ?></a>

<?php require_once("../shared/footer.php"); ?>

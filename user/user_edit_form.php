<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "user";
  $restrictToMbrAuth = TRUE;
  $nav = "edit";
  $focus_form_name = "editMbrform";
  $focus_form_field = "barcodeNmbr";
  require_once("../functions/inputFuncs.php");
  require_once("../user/logincheck.php");
  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");

 
  $mbrid = $_SESSION["mbrid"];

  $mbrQ = new MemberQuery();
  $mbrQ->connect();
  $mbr = $mbrQ->get($mbrid);
  $mbrQ->close();
  require_once("../opac/header_opac.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  $headerWording = $loc->getText("mbrEditForm");

  $cancelLocation = "../user/user_view.php?mbrid=".$mbrid."&reset=Y";
?>
<link rel="stylesheet" href="../css/material/material.min.css">
<script src="../css/material/material.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="../css/material/material-kit.min.css">

 <div class="main main-raised" style="margin-top: 20px; background: #e8e8e8">
    <div class="profile-content">
        <div class="demo-layout-transparent mdl-layout mdl-js-layout">
          <div class="mdl-layout__drawer" style="border-radius: 6px">
          <span class="mdl-layout-title">Opciones</span>
          <nav class="mdl-navigation">
            <a class="mdl-navigation__link" href="../user/user_view.php?mbrid=<?php echo HURL($mbrid);?>">Información</a>
            <a class="mdl-navigation__link" style="background: #c5c5c5">Editar datos</a>
            <a class="mdl-navigation__link" href="../user/user_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y">Cuenta</a>
            <a class="mdl-navigation__link" href="../user/user_history.php?mbrid=<?php echo HURL($mbrid);?>">Historial</a>
          </nav>
          </div>
        </div>
        
        <div class="container">

          <div class="mdl-card mdl-shadow--2dp" style="width: auto; float: none; margin: 0 auto; margin-top: 50px; margin-bottom: 20px;">

<script type="text/javascript">
  document.getElementById("body").classList.add('userhome');
</script>

            <form ENCTYPE="multipart/form-data" name="editMbrform" method="POST" action="../user/user_edit.php">
            <input type="hidden" name="mbrid" value="<?php echo H($mbrid);?>">
            <input type="hidden" name="MAX_FILE_SIZE" value=OBIB_MAX_FILE_SIZE>
            <?php include("../user/user_fields.php"); ?>

<script type="text/javascript">
  var fc = document.getElementsByClassName("form-control");
  while(fc.length > 0) {
    fc[0].className='mdl-textfield__input';
  }
  var py = document.getElementsByClassName("primary");
  while(py.length > 0) {
    py[0].className='text-table';
  }
</script>

          </div>
        </div>
      </div>
    </div>

<?php include("../shared/footer.php"); ?>
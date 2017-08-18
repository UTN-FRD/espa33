<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "admin";
  $nav = "summary";
  $helpPage = "admin";

  include("../shared/logincheck.php");
  include("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

?>

<h3><img src="../images/admin.png" border="0" width="30" height="30" align="top" /> <?php echo $loc->getText("indexHdr");?></h3>
<?php echo $loc->getText("indexDesc");?><br><br>
<input type="button" class="btn btn-default" onclick="location.href='../opac/index.php';" value="Ir al OPAC" />


<?php include("../shared/footer.php"); ?>

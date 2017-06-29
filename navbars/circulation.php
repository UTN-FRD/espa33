<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../classes/Localize.php");
  $navLoc = new Localize(OBIB_LOCALE,"navbars");
 
?>
<!--
<input type="button" onClick="self.location='../shared/logout.php'" value="<?php echo $navLoc->getText("logout"); ?>" class="navbutton"><br />
<br />-->

<ul id="nav-tabs-wrapper" class="nav nav-pills nav-stacked well">
<?php if ($nav == "searchform") { ?>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("memberSearch"); ?>
   </a>
 </li>
<?php } else { ?>
 <li>
   <a href="../circ/index.php" class="alt1"><?php echo $navLoc->getText("memberSearch"); ?></a>
 </li>
<?php } ?>

<?php if ($nav == "search") { ?>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("catalogResults"); ?>
   </a>
 </li>
<?php } ?>

<?php if ($nav == "view") { ?>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("memberInfo"); ?>
   </a>
 </li>
 <li>
   <a href="../circ/mbr_edit_form.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("editInfo"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("catalogDelete"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y" class="alt1"><?php echo $navLoc->getText("account"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_history.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("checkoutHistory"); ?></a>
 </li>
<?php } ?>

<?php if ($nav == "edit") { ?>
 <li>
   <a href="../circ/mbr_view.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("memberInfo"); ?></a>
 </li>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("editInfo"); ?>
   </a>
 </li>
 <li>
   <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("catalogDelete"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y" class="alt1"><?php echo $navLoc->getText("account"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_history.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("checkoutHistory"); ?></a>
 </li>
<?php } ?>

<?php if ($nav == "delete") { ?>
 <li>
   <a href="../circ/mbr_view.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("memberInfo"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_edit_form.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("editInfo"); ?></a>
 </li>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("catalogDelete"); ?>
   </a>
 </li>
 <li>
   <a href="../circ/mbr_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y" class="alt1"><?php echo $navLoc->getText("account"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_history.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("checkoutHistory"); ?></a>
 </li>
<?php } ?>

<?php if ($nav == "hist") { ?>
 <li>
   <a href="../circ/mbr_view.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("memberInfo"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_edit_form.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("editInfo"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("catalogDelete"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_account.php?mbrid=<?php echo HURL($mbrid);?>&amp;reset=Y" class="alt1"><?php echo $navLoc->getText("account"); ?></a>
 </li>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("checkoutHistory"); ?>
   </a>
 </li>
<?php } ?>

<?php if ($nav == "account") { ?>
 <li>
   <a href="../circ/mbr_view.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("memberInfo"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_edit_form.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("editInfo"); ?></a>
 </li>
 <li>
   <a href="../circ/mbr_del_confirm.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("catalogDelete"); ?></a>
 </li>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("account"); ?>
   </a>
 </li>
 <li>
   <a href="../circ/mbr_history.php?mbrid=<?php echo HURL($mbrid);?>" class="alt1"><?php echo $navLoc->getText("checkoutHistory"); ?></a>
 </li>
<?php } ?>

<?php if ($nav == "new") { ?>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("newMember"); ?>
   </a>
 </li>
<?php } else { ?>
 <li>
   <a href="../circ/mbr_new_form.php?reset=Y" class="alt1"><?php echo $navLoc->getText("newMember"); ?></a>
 </li>
<?php } ?>

<?php if ($nav == "checkin") { ?>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("checkIn"); ?>
   </a>
 </li>
<?php } else { ?>
 <li>
   <a href="../circ/checkin_form.php?reset=Y" class="alt1"><?php echo $navLoc->getText("checkIn"); ?></a>
 </li>
<?php } ?>

<?php if ($nav == "csv_import") { ?>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("MemberCSVImport"); ?>
   </a>
 </li>
<?php } else { ?>
 <li>
   <a href="../circ/csv_import.php" class="alt1"><?php echo $navLoc->getText("MemberCSVImport"); ?></a>
 </li>
<?php } ?>


<?php if ($nav == "offline") { ?>
 <li class="active">
   <a data-toggle="tab">
   <?php echo $navLoc->getText("offline"); ?>
   </a>
 </li>
<?php } else { ?>
 <li>
   <a href="../circ/offline.php" class="alt1"><?php echo $navLoc->getText("offline"); ?></a>
 </li>
<?php } ?>


 <li>
   <a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".H(addslashes(U($helpPage))); ?>')"><?php echo $navLoc->getText("help"); ?></a>
 </li>
</ul>

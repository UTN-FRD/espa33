<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 if (preg_match('/[^a-zA-Z0-9_]/', $tab)) {
   Fatal::internalError("Possible security violation: bad tab name");
   exit(); # just in case
 }
  include("../shared/header_top.php");
  //include("../shared/navbar.php");
;?>

    <!-- **************************************************************************************
     * Main body y left nav
     **************************************************************************************-->

    <div class="row content">
    <!-- **************************************************************************************
     * Left nav
     **************************************************************************************-->

      <div class="col-md-3 nav" style="width: 20%;">
        <?php include("../navbars/".$tab.".php");?>
      </div>
    <div class="col-md-9">
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->

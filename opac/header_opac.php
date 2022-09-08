<?php
  include("../opac/header_top_opac.php");
  //include("navbar_opac.php");
?>

    <!-- **************************************************************************************
     * Main body y top nav
     **************************************************************************************-->
    <!--
    <link rel="stylesheet" href="../css/material/Material.min.css">
    <script src="../css/material/material.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    -->
     <?php if ($tab != "opac" and $nav != "user_view" and $nav != "user_pass") { ?>
      <div class="row" style="/*background-color: rgb(255, 255, 255, 0.28);*/">
        <?php //include("../navbars/opac.php");?>
      </div> <?php } ?>

    <div class="row content">
    <!-- **************************************************************************************
     * Top nav
     **************************************************************************************-->
    
    <div class=<?php if ($tab != "opac") {echo "'col-md-12 userhomecol'";} else {echo "'row row-centered' style='max-width:950'";} ?> >
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->



<?php
  include("../opac/header_top_opac.php");
  //include("navbar_opac.php");
?>

    <!-- **************************************************************************************
     * Main body y left nav
     **************************************************************************************-->

    <div class="row content">
    <!-- **************************************************************************************
     * Left nav
     **************************************************************************************-->
    <?php if ($tab != "opac") { ?>
      <div class="col-md-3 nav" style="width: 20%;">
        <?php include("../navbars/opac.php");?>
      </div> <?php } ?>
    <div class=<?php if ($tab != "opac") {echo "'col-md-9'";} else {echo "'row row-centered' style='max-width:950'";} ?> >
<!-- **************************************************************************************
     * beginning of main body
     **************************************************************************************-->



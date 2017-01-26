<!-- **************************************************************************************
     * Tabs
     **************************************************************************************-->
<ul class="nav nav-tabs">
  <li role="presentation" class="<?php if ($tab == 'user') { echo 'active'; } ?>">
    <a href="../user/index.php">
      <?php echo $headerLoc->getText('headerCirculation');?>
    </a>
  </li>
  <li role="presentation" class="<?php if ($tab == 'opac') { echo 'active'; } ?>">
    <a href="../opac/index.php">
      <?php echo $headerLoc->getText('headerOpac'); ?>
    </a>
  </li>
</ul>

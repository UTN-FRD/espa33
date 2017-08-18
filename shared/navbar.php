<!-- **************************************************************************************
     * Tabs
     **************************************************************************************-->

<div class="row">

  <ul class="nav nav-tabs">
    <li role="presentation" class="<?php if ($tab == 'home') { echo 'active'; } ?>">
      <a href="../home/index.php">
        <?php echo $headerLoc->getText('headerHome');?>
      </a>
    </li>
    <li role="presentation" class="<?php if ($tab == 'circulation') { echo 'active'; } ?>">
      <a href="../circ/index.php">
        <?php echo $headerLoc->getText('headerCirculation'); ?>
      </a>
    </li>
    <li role="presentation" class="<?php if ($tab == 'cataloging') { echo 'active'; } ?>">
      <a href="../catalog/index.php">
        <?php echo $headerLoc->getText('headerCataloging'); ?>
      </a>
    </li>
    <li role="presentation" class="<?php if ($tab == 'admin') { echo 'active'; } ?>">
      <a href="../admin/index.php">
        <?php echo $headerLoc->getText('headerAdmin'); ?>
      </a>
    </li>
    <li role="presentation" class="<?php if ($tab == 'reports') { echo 'active'; } ?>">
      <a href="../reports/index.php">
        <?php echo $headerLoc->getText('headerReports'); ?>
      </a>
    </li>
    <!--<li role="presentation" class="<?php if ($tab == 'opac') { echo 'active'; } ?>">
      <a href="../opac/index.php">
        <?php echo $headerLoc->getText('headerOpac'); ?>
      </a>
    </li>-->


        
    <?php
    if (!$_SESSION["hasCircAuth"]) {
      echo "

      ";
    } elseif (isset($restrictToMbrAuth) and !$_SESSION["hasCircMbrAuth"]) {
      echo "

      ";
    }
    else{ ?>
      
      <li id="btnsalir" role="presentation">
        <a onClick="self.location='../shared/logout.php'">
          Salir
        </a>
      </li>
    
    <?php } ?>


      

  </ul>

</div>



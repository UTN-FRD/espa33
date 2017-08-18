<!-- **************************************************************************************
     * Tabs
     **************************************************************************************-->
<div class="row">

<ul class="nav nav-tabs">
  <li role="presentation" class="<?php if ($tab == 'user') { echo 'active'; } ?>">
    <a href="../user/user_view.php">
      <?php echo $headerLoc->getText('headerCirculation');?>
    </a>
  </li>
  <li role="presentation" class="<?php if ($tab == 'opac') { echo 'active'; } ?>">
    <a href="../opac/index.php">
      <?php echo $headerLoc->getText('headerOpac'); ?>
    </a>
  </li>


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
        <a onClick="self.location='../user/logout.php'">
          Salir
        </a>
      </li>
    
    <?php } ?>

    </ul>


</div>
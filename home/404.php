<?php
  require_once("../shared/common.php");
  session_cache_limiter(null);

  $tab = "home";
  $nav = "404";

  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE, $tab);

  require_once("../opac/header_opac.php");
?>

<link rel="stylesheet" type="text/css" href="../css/component.css" />
<link rel="stylesheet" href="../css/material/select/getmdl-select.min.css">
<script defer src="../css/material/select/getmdl-select.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#main-content').fadeIn();
});
</script>

<div id="main-content">
  <div class="row" id="titulo" style="/*margin-top: 25%*/">

  </div>


</div>

<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../classes/Localize.php");
  $headerLoc = new Localize(OBIB_LOCALE,"shared");

// code html tag with language attribute if specified.
echo "<html";
if (OBIB_HTML_LANG_ATTR != "") {
  echo " lang=\"".H(OBIB_HTML_LANG_ATTR)."\"";
}
echo ">\n";
echo "<head>\n";

// JALG, sE AGREGA LINEA PARA QUE APARESCA LA IMAGEN EN LA PESTAÑA DEL EXPLORADOR, CAMBIAR FAVICON.ICO EN LA rAIZ
echo "<link href='../favicon.ico' rel='icon' type='image/x-icon'/>";

// -- codigo agregado en la sf
echo "<link href='../css/bootstrap.min.css' rel='stylesheet'>";
// --
// code character set if specified
if (OBIB_CHARSET != "") { ?>
  <meta http-equiv="content-type" content="text/html; charset=<?php echo H(OBIB_CHARSET); ?>">
  <meta charset=<?php echo H(OBIB_CHARSET); ?>">

<?php } ?>
  <meta name="description" content="Sistema informático para la gestión de bibliotecas y biblioteca digital, basado en Openbiblio">

<link href="../css/style.css" rel="stylesheet" type="text/css" media="screen" />
<style type="text/css">
  <?php //include("../css/style.php");?>
</style>
<?php
  if (!isset($_SESSION["active_theme"])) {
    require_once("../shared/theme.php");
    $_SESSION["active_theme"] = get_active_theme();
  }
  if (strcmp($_SESSION["active_theme"], "") != 0) {
    echo '<link href="../themes/'. $_SESSION["active_theme"] .'/style.css?ver=1.1" rel="stylesheet" type="text/css" media="screen" />';
  }
?>
<title><?php echo H(OBIB_LIBRARY_NAME);?></title>
<script language="JavaScript">
<!--
function popSecondary(url) {
    var SecondaryWin;
    SecondaryWin = window.open(url,"secondary","resizable=yes,scrollbars=yes,width=535,height=400");
    self.name="main";
}
function popSecondaryLarge(url) {
    var SecondaryWin;
    SecondaryWin = window.open(url,"secondary","toolbar=yes,resizable=yes,scrollbars=yes,width=700,height=500");
    self.name="main";
}
function backToMain(URL) {
    var mainWin;
    mainWin = window.open(URL,"main");
    mainWin.focus();
    this.close();
}
-->
</script>
<?php
	## ---------------------------------------------------------------------
	## --- added for Fred LaPlante's Lookup Function -----------------------
	## --- modificado jalg para que solo se use en funciones de lookup -----

	//echo $nav;
if ($nav=="lookupOpts" || $nav=="lookupHosts" || $nav=="lookup" ){
	if (file_exists('../lookup2/customHead.php')) {
		include ('../lookup2/customHead.php');
			echo "lara";
	}
}
	## ---------------------------------------------------------------------
	?>

  <script type="text/javascript" src="../scripts/jquery.js"></script>
  <script type="text/javascript" src="../scripts/jquery.collapsible.js"></script>
  <?php
    if (file_exists('../scripts/locale/'. OBIB_LOCALE .'.js')) {
      $js_filename = OBIB_LOCALE .'.js';
    } else {
      $js_filename = 'en.js';
    }
  ?>
  <script type="text/javascript" src="../scripts/locale/<?php echo $js_filename; ?>"></script>
  <script type="text/javascript" src="../scripts/search.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script type="text/javascript">
      $(window).on('load',function(){
        $('#myModal').modal('show');
      });
  </script>

</head>
<body id="<?php if ($nav == "home") {echo "homebody";} else {echo "";} ?>" bgcolor="<?php echo H(OBIB_PRIMARY_BG);?>" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" <?php
  if (isset($focus_form_name) && ($focus_form_name != "")) {
    if (preg_match('/^[a-zA-Z0-9_]+$/', $focus_form_name)
        && preg_match('/^[a-zA-Z0-9_]+$/', $focus_form_field)) {
      echo 'onLoad="self.focus();document.'.$focus_form_name.".".$focus_form_field.'.focus()"';
    }
  } ?> >

<!-- **************************************************************************************
     * Google
     **************************************************************************************-->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','https://www.google-analytics.com/analytics.js','ga'); ga('create', 'UA-80143634-3', 'auto'); ga('send', 'pageview');
</script>

<!-- **************************************************************************************
     * Library Name and hours
     **************************************************************************************-->
<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet">

<div class="container-fluid">
<div id="push">
<div class="row fondo">
<div class="row header">
   <div class="col-sm-3 title">
      <?php
        if (OBIB_LIBRARY_IMAGE_URL != "") {
          echo "<img id=\"utn\" src=\"".H(OBIB_LIBRARY_IMAGE_URL)."\" >";
        }
        if (!OBIB_LIBRARY_USE_IMAGE_ONLY) {
          echo ' <a href="'. DOCUMENT_ROOT .'" class="library-name">'. H(OBIB_LIBRARY_NAME) .'</a>';
        }
      ?>
   </div>

   <div class="col-sm-9">
    <div class="row">
      <ul class="navhome nav nav-tabs navbar-right">
        <li class="<?php if ($tab == 'home') { echo 'active'; } ?>">
          <a href="../home/index.php">
            <?php echo $headerLoc->getText('headerHome');?>
          </a>
        </li>
        <li class="<?php if ($tab == 'circulation') { echo 'active'; } ?>">
          <a href="../circ/index.php">
            <?php echo $headerLoc->getText('headerCirculation'); ?>
          </a>
        </li>
        <li class="<?php if ($tab == 'cataloging') { echo 'active'; } ?>">
          <a href="../catalog/index.php">
            <?php echo $headerLoc->getText('headerCataloging'); ?>
          </a>
        </li>
        <li class="<?php if ($tab == 'admin') { echo 'active'; } ?>">
          <a href="../admin/index.php">
            <?php echo $headerLoc->getText('headerAdmin'); ?>
          </a>
        </li>
        <li class="<?php if ($tab == 'reports') { echo 'active'; } ?>">
          <a href="../reports/index.php">
            <?php echo $headerLoc->getText('headerReports'); ?>
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
          
          <li>
            <a onClick="self.location='../shared/logout.php'" id="btnsalir" class="glyphicon glyphicon-log-out">
              
            </a>
          </li>
        
        <?php } ?>
      </ul>
    </div>
  </div>
  </div>
</div>



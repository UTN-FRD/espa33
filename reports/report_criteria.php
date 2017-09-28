<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");

  $tab = "reports";
  $nav = "reportcriteria";

  require_once("../shared/logincheck.php");
  require_once("../classes/Report.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  $navLoc = new Localize(OBIB_LOCALE, 'navbars');

  if (isset($_SESSION['postVars']['type'])) {
    $type = $_SESSION['postVars']['type'];
  } elseif (isset($_REQUEST['type'])) {
    $type = $_REQUEST['type'];
  } else {
    header('Location: ../reports/index.php');
    exit(0);
  }
  
  list($rpt, $err) = Report::create_e($type);
  if ($err) {
    header('Location: ../reports/index.php');
    exit(0);
  }

  Nav::node('reportcriteria', $navLoc->getText('reportsCriteria'));
  include("../shared/header.php");

  #****************************************************************************
  #*  getting form vars
  #****************************************************************************
  require("../shared/get_form_vars.php");

  echo '<h3>'.$loc->getText($rpt->title()).'</h3>';

  if (isset($_REQUEST['msg'])) {
    echo '<p><font class="error">'.H($_REQUEST['msg']).'</font></p>';
  }
?>

<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'></script>
<script type="text/javascript">
$(document).ready(function() { 
    $("#rpt_out_since_day").val("1");
    $("#rpt_out_since_month").val("01");
    $("#rpt_out_since_year").val("1997");
  });

</script>

<div class="col col-md-4">

<form name="reportcriteriaform" method="GET" action="../reports/run_report.php">
<input type="hidden" name="type" value="<?php echo H($rpt->type()) ?>" />

<?php
  $format = array(
    array('select', '__format', array('title'=>'Format'), array(
      array('paged', array('title'=> $loc->getText('HTML (page-by-page)'))),
      array('html', array('title'=>$loc->getText('HTML (one big page)'))),
      array('csv', array('title'=>$loc->getText('CSV'))),
      array('xls', array('title'=>$loc->getText('Microsoft Excel'))),
    )),
  );
  $params = array_merge($rpt->paramDefs(), $format);
  Params::printForm($params);
?>
<br>

<input type="submit" value="<?php echo $loc->getText('Submit'); ?>" class="btn btn-primary" />
</form>

</div>
<?php include("../shared/footer.php"); ?>

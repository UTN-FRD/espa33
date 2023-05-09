<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "admin";
  $nav = "summary";
  $helpPage = "admin";

  include("../shared/logincheck.php");
  include("../shared/header.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

?>

<h3><?php echo $loc->getText("indexHdr");?></h3>
<hr/>  
<br>
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Información de la biblioteca</h3>
      </div>
      <div class="nopadding panel-body">
         <table class="nomarginbottom nomargin table">
           <tr>
             <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_HOURS != "") echo $headerLoc->getText("headerLibraryHours");?></font></td>
             <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_HOURS != "") echo H(OBIB_LIBRARY_HOURS);?></font></td>
           </tr>
           <tr>
             <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_ADERS != "") echo $headerLoc->getText("headerLibraryAders");?></font></td>
             <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_ADERS != "") echo H(OBIB_LIBRARY_ADERS);?></font></td>
           </tr>
           <tr>
             <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_PHONE != "") echo $headerLoc->getText("headerLibraryPhone");?></font></td>
             <td class="title" nowrap="yes"><font><?php if (OBIB_LIBRARY_PHONE != "") echo H(OBIB_LIBRARY_PHONE);?></font></td>
           </tr>
         </table>
      </div>
    </div>
  </div>
</div>
<button type="button" class="btn btn-default" onclick="location.href='../opac/index.php';">Ir al OPAC</button>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modaladmin">Desarrolladores</button>

    <div id="modaladmin" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Desarrolladores</h4>
          </div>
          <div class="modal-body">
            <h5>OpenBiblio versión 0.7.2 de Tim Dave Stevens (2002-2014)<br>
                Jorge Lara Cravero (2005-2008)<br>
                El equipo de desarrollo de Espabiblio 3.3 (José Antonio Lara Galindo) (2009-2015)<br>
                Software Factory, <a style="font-family: sans-serif" href="mailto:brunosagaste@hotmail.com?Subject=Sistema%20de%20biblioteca%20">Bruno Sagaste</a>, Bruno Fernandez y Gonzalo Pacher del Laboratorio de Sistemas de Información de la UTN Facultad Regional Delta (2015-2023)<br>
                Fork de OpenBiblio 0.7.1 y EspaBiblio 3.3 Giordano Bruno
            </h5>
          </div>
          <div class="modal-footer">
            <a href="../doc/changelog.php" class="btn btn-default">Documentos</a>
            <a href="https://github.com/UTN-FRD/espa33" class="btn btn-default">GitHub</a>
            <a href="http://obiblio.sourceforge.net/" class="btn btn-default">OpenBiblio</a>
            <a href="https://sourceforge.net/p/espabiblio/wiki/Home/" class="btn btn-default">EspaBiblio</a>
            <a href="https://lsi.frd.utn.edu.ar/" class="btn btn-default">LSI</a>
          </div>
        </div>

      </div>
    </div>


<?php include("../shared/footer.php"); ?>

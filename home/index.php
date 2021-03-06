<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "home";
  $nav = "home";

  require_once("../shared/logincheck.php");
  require_once("../shared/header_top.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  $user = $_SESSION["username"];

  $vector = array(
    1 => "¿Que vamos a hacer hoy?",
    2 => "¿De nuevo por acá?",
    3 => "¡Tanto tiempo!",
    4 => "¡No te ovides de leer los cambios!",
    5 => "¿Por qué un libro de matemáticas siempre es infeliz?<br>Porque siempre tiene muchos problemas"
);

// Obtenemos un número aleatorio
$numero = rand(1,4);

?>

<link rel="stylesheet" type="text/css" href="../css/component.css" />
<script src="../js/modernizr.custom.js"></script>
<link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#main-content').fadeIn();
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>


<div class="container" id="main-content">
    <div class="row" id="titulo">
    <p class="m_title">¡Hola <?php echo $user ?>!</p>
    <p style="font-size: 30px"><?php echo "$vector[$numero]" ?></p>
    </div>


        <div class="hi-icon-wrap hi-icon-effect-1 hi-icon-effect-1a">
          <a href="../shared/logout.php" class="hi-icon hi-icon-user">Opac</a>
          <a href="../circ/index.php" class="hi-icon hi-icon-bookmark">Prestamos</a>
          <a href="../catalog/index.php" class="hi-icon hi-icon-archive">Catalogación</a>
          <a href="../admin/index.php" class="hi-icon hi-icon-cog">Administración</a>
          <a href="../reports/index.php" class="hi-icon hi-icon-list">Reportes y estadísticas</a>
        </div>

    <div class="text-center">
      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalhome">Cambios 28/9</button>
    </div>

    <div id="modalhome" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Cambios v3.2.3</h4>
          </div>
          <div class="modal-body" id="#modalBodyHome">
            <h5>v3.2.3 - 28/9/17<br>
                Corregido error al guardar un usuario con fecha de nacimiento 0000-00-00.<br>
                Corregido error que impedía mostrar errores al editar un usario.<br>
                Actualizaciones de seguridad introducidas en OpenBiblio 7.2<br>
                <hr style="margin: 10; width: 100%">
                v3.2.2 - 27/9/17<br>
                Corregido error que actualizaba la fecha de prestado a la fecha de la última renovación.<br>
                <hr style="margin: 10; width: 100%">
                v3.2.1 - 26/9/17<br>
                Agregados tooltips a los botones de devolución y renovación.<br>
                <hr style="margin: 10; width: 100%">
                v3.2 - 25/9/17<br>
                Interfaz actualizada.<br>
                Corregido error al buscar un libro con un solo resultado en el OPAC.<br>
                Corregido error al eliminar una reserva desde el OPAC.<br>
                Aviso:<br>
                <strong>El sistema ya puede ser utilizado por los socios.</strong><br>
                Es posible desactivar el bloqueo de préstamos cuando hay pendiente una multa desde la configuración de la biblioteca.<br>
                Si no se desactiva, se puede cambiar la multa por retraso a 0 en colecciones.<br>
                Si existe una multa, debe ser pagada en cuenta, dentro de la vista de socio, hasta dejar el balance en 0.<br>
                Recordar que no es posible prestar si existe una multa. Pagarla con el procedimiento del punto anterior en lugar de eliminarla.<br>
                <hr style="margin: 10; width: 100%">
                v3.1 - 1/8/17<br>
                La renovación de libros está funcionando. Recordar que no es posible renovar si el libro está vencido.<br>
                Al renovar un libro, se suman los dias de renovación a la fecha de devolucion.<br>
                Se corrigió el error que impedía hacer click en editar, en la vista de información de libro.<br>
                Los códigos RFID se cargan en editar, en la vista de información de libro.<br>
                Es posible buscar, prestar y devolver un libro a través del código RFID.<br>
                Es posible aumentar la duración de la sesión en configuración de la biblioteca, dentro de administración.<br>
                Si al buscar un libro hay un solo resultado encontrado, se envía a este automáticamente.
            </h5>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>

      </div>
    </div>

    <script>
      var hash = window.location.hash,
        current = 0,
        demos = Array.prototype.slice.call( document.querySelectorAll( '#codrops-demos > a' ) );
      
      if( hash === '' ) hash = '#set-1';
      setDemo( demos[ parseInt( hash.match(/#set-(\d+)/)[1] ) - 1 ] );

      demos.forEach( function( el, i ) {
        el.addEventListener( 'click', function() { setDemo( this ); } );
      } );

      function setDemo( el ) {
        var idx = demos.indexOf( el );
        if( current !== idx ) {
          var currentDemo = demos[ current ];
          currentDemo.className = currentDemo.className.replace(new RegExp("(^|\\s+)" + 'current-demo' + "(\\s+|$)"), ' ');
        }
        current = idx;
        el.className = 'current-demo'; 
      }
    </script>
</div>


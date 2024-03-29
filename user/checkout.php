<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "user";
  $nav = "view";
  $restrictInDemo = true;
  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioQuery.php");
  require_once("../user/logincheck.php");
  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/BiblioStatusHist.php");
  require_once("../classes/BiblioStatusHistQuery.php");
  require_once("../classes/MemberQuery.php");
  require_once("../classes/MemberAccountQuery.php");
  require_once("../classes/Date.php");
  require_once("../functions/errorFuncs.php");
  require_once("../functions/formatFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
    
  $foundError = false;

  #****************************************************************************
  #*  Checking for post or get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_GET) != 0) {
      $_POST = $_GET;
  }
  if (count($_POST) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }
  //$searchType = trim($_POST["searchType"]);

  $barcode = trim($_POST["barcodeNmbr"]);
  $mbrid = $_SESSION["mbrid"];
  $mbrQ = new MemberQuery;
  $mbrQ->connect();
  $mbr = $mbrQ->get($mbrid);
  $mbrClassification = $mbr->getClassification();
  
  if (strcmp($mbr->getStatus(), "N") == 0) {
    $foundError = TRUE;
    $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr9");
  }

  if(isset($_POST["renewal"])) {
      $renewal = true;
  }
  else {
      $renewal = false;
  }

  #****************************************************************************
  #*  Make sure member does not have outstanding balance due
  #****************************************************************************
  if (OBIB_BLOCK_CHECKOUTS_WHEN_FINES_DUE) {
    $acctQ = new MemberAccountQuery();
    $acctQ->connect();
    if ($acctQ->errorOccurred()) {
      $acctQ->close();
      displayErrorPage($acctQ);
    }
    $balance = $acctQ->getBalance($mbrid);
    if ($acctQ->errorOccurred()) {
      $acctQ->close();
      displayErrorPage($acctQ);
    }
    $acctQ->close();
    if ($balance > 0) {
      $pageErrors["barcodeNmbr"] = $loc->getText("checkoutBalErr");
      $postVars["barcodeNmbr"] = $barcode;
      $_SESSION["postVars"] = $postVars;
      $_SESSION["pageErrors"] = $pageErrors;
      header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
      exit();
    }
  }

  #****************************************************************************
  #*  Edit input
  #****************************************************************************
  if (!ctypeAlnum(trim($barcode))) {
      $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr1");
      $_SESSION["postVars"] = $_POST;
      $_SESSION["pageErrors"] = $pageErrors;
      header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
      exit();
  }

  #****************************************************************************
  #*  Read copy record
  #****************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if (!$copy = $copyQ->queryByBarcode($barcode)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  
  $copyQ2 = new BiblioCopyQuery();
  $copyQ2->connect();
  if ($copyQ2->errorOccurred()) {
    $copyQ2->close();
    displayErrorPage($copyQ2);
  }
  if (!$copy2 = $copyQ2->execSelect($copy->getBibid())) {
    $copyQ2->close();
    displayErrorPage($copyQ2);
  }
  $copies = $copyQ2->getRowCount();/*Cuento cantidad de copias*/
  $copiesIn = 0;
  while ($copyCirc = $copyQ2->fetchCopy()) {/*Cuento la cantidad de copias que están prestadas*/
    if ($copyCirc->getDueBackDt() == '') {
      $copiesIn++;
    }
  }

  #*****************************************************************************************************************
  #*  Edit results                                                                                                 * 
  #*  Modificado por requerimientos de la biblioteca UTN FRD.                                                      *
  #*  No permitir una renovación si hay una reserva, si hay una sola copia, o si todas las copias estan prestadas. *
  #*****************************************************************************************************************
  if ($copyQ->getRowCount() == 0) {
    $foundError = true;
    $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr2");
  } else {
    $daysDueBack = $copyQ->getDaysDueBack($copy); //getDaysDueBack adds 1 day for every weekend day
    if ($copyQ->errorOccurred()) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
    if(isset($_POST['date_from']) && isset($_POST['dueDate']) && $_POST['date_from'] == 'override'){
      list($dueDate, $err) = Date::read_e($_POST['dueDate']);
      if($err) {
        $pageErrors["dueDate"] = $loc->getText("Bad date: %err%", array('err'=>$err->toStr()));
        $_SESSION["postVars"] = $_POST;
        $_SESSION["pageErrors"] = $pageErrors;
        header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
        exit();
      }
      $_SESSION['due_date_override'] = $_POST['dueDate'];
    } else {
      if (!$renewal) {
        header("Location: ../user/user_view.php?mbrid=".U($mbrid));
      }
    }
    if ($copy->getStatusCd() == OBIB_STATUS_OUT) {
      //Item already checked out, let's see if it's a renewal
      if($renewal) {
        //Check to see if the renewal limit has been reached
        $reachedLimit = $copyQ->hasReachedRenewalLimit($mbrid, $mbrClassification, $copy);
        if ($copyQ->errorOccurred()) {
          $copyQ->close();
          displayErrorPage($copyQ);
        }
        if ($reachedLimit) {
          $foundError = TRUE;
          $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr7",array("barcode"=>$barcode));
        }
        if ($copies==1) { /*Acá entra, pero si hay una sola copia, como esta prestada, tambien entra en el if de abajo y el error que queda es el segundo. 
                            Si funciona bien puede eliminarse, lo mantengo por seguridad.*/
          $foundError = TRUE;
          $pageErrors["barcodeNmbr"] = $loc->getText("No es posible renovar un libro con una sola copia.",array("barcode"=>$barcode));
        }
        if ($copiesIn==0) { //Verifico si todas las copias están prestadas.
          $foundError = TRUE;
          $pageErrors["barcodeNmbr"] = $loc->getText("No es posible renovar un libro con todas sus copias prestadas o con una sola copia.",array("barcode"=>$barcode));
        }
        else {
          if($copy->getDaysLate() > 0) {
            $foundError = TRUE;
            $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr8",array("barcode"=>$barcode));
          }
          else { 
            //Ckeck for existing hold. Need to close copyQ connection so we can call hold functions
            $bibid = $copy->getBibid();
            $copyQ->close();

            $holdQ = new BiblioHoldQuery();
            $holdQ->connect();
            if ($holdQ->errorOccurred()) {
              $holdQ->close();
              displayErrorPage($holdQ);
            }
            //Check if there is a hold for that material
            $hold = $holdQ->getFirstHold($bibid,$barcode);

            //If there is a hold, getFirstHold will return the material info. If not, will return false and we can continue
            //Si hay una reserva, getFirstHold devuelve la información del material, sino devuelve falso y podemos continuar.            
            if ($hold != false) {
                $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr10",array("barcode"=>$barcode));
                $postVars["barcodeNmbr"] = $barcode;
                $_SESSION["postVars"] = $postVars;
                $_SESSION["pageErrors"] = $pageErrors;
                header("Location: ../user/user_view.php?mbrid=".U($mbrid));
                exit();
            }
            //Need to reestablish copyQ connection
            $holdQ->close();

            $copyQ->connect();
            if ($copyQ->errorOccurred()) {
              $copyQ->close();
              displayErrorPage($copyQ);
            }
            //Revisamos que la fecha de renovación sea mayor que la fecha de devolución menos los días configurados
            $readyToRenew = $copyQ->readyToRenew($mbrid, $mbrClassification, $copy);
            if (!$readyToRenew["result"]) {
                $foundError = True;
                $pageErrors["barcodeNmbr"] = $loc->getText("Renovable a partir del " . $readyToRenew["dateavailable"] , array("barcode"=>$barcode));
            }

            //We can renew this item! Renew for a due back period adding 1 day for every weekend day
            //Se puede renovar el material. Se renueva por un período de devolución sumando un dia por cada día del fin de semana.
            $date = $copy->getDueBackDt(); //Get return date. Obtengo fecha de devolución.
            $daysDueBackWithRenew = $copyQ->getDaysDueBack($copy, $date); //Send date of material return and return new days of renewal (using due back date of material) + weekend days.
                                                                          //Envío fecha de devolución y obtengo los nuevos días, usando la fecha anterior + fines de semanas.
            $copy->setRenewalCount($copy->getRenewalCount() + $daysDueBackWithRenew*24); //Set new renewal hours. Guardo las horas.
            //Add renewal days to due back date. Sumo los días a la fecha de renovación.
            $days = $copy->getRenewalCount()/24;
            $newDate = Date::addDays($copy->getDueBackDt(), $daysDueBackWithRenew);
            $copy->setDueBackDt($newDate);
            $copy->setLastRenewalBy("member");
          }
        }
      }
      else {
        //copy is already checked out by someone else
        $foundError = TRUE;
        $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr3",array("barcode"=>$barcode));
      }
    } else {
      //Not a renewal, clearing renewal count
      $copy->setRenewalCount(0);
      
      // check days due back
      // some collections will have days due back set to 0 so that those items can not be checked out.
      if ($daysDueBack <= 0) {
        $foundError = true;
        $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr4",array("barcode"=>$barcode));
      } else {
        // check to see if collection max has been reached
        $reachedLimit = $copyQ->hasReachedCheckoutLimit($mbrid,$mbrClassification,$copy->getBibid());
        if ($copyQ->errorOccurred()) {
          $copyQ->close();
          displayErrorPage($copyQ);
        }
        if ($reachedLimit) {
          $foundError = TRUE;
          $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr6");
        }
      }
    }
  }

  #**************************************************************************
  #*  return to member view if there are checkout errors to show
  #**************************************************************************
  if ($foundError == TRUE) {
    $copyQ->close();
    $postVars["barcodeNmbr"] = $barcode;
    $_SESSION["postVars"] = $postVars;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../user/user_view.php?mbrid=".U($mbrid));
    exit();
  }

  #**************************************************************************
  #*  Show hold edit if bibliography is currently on hold and
  #*  current member != first member in hold queue
  #**************************************************************************
  if ($copy->getStatusCd() == OBIB_STATUS_ON_HOLD) {
    // need to close copyQ connection so we can call hold functions
    $copyQ->close();
    // check copy hold queue
    $holdQ = new BiblioHoldQuery();
    $holdQ->connect();
    if ($holdQ->errorOccurred()) {
      $holdQ->close();
      displayErrorPage($holdQ);
    }
    $hold = $holdQ->getFirstHold($copy->getBibid(),$copy->getCopyid());
    if ($holdQ->errorOccurred()) {
      $holdQ->close();
      displayErrorPage($holdQ);
    }
    // make sure hold still exists.  if not continue on with checkout
    if ($holdQ->getRowCount() > 0) {
      $holdAge = time() - strtotime($hold->getHoldBeginDt());
      $tooOld = false;
      $secondsPerDay = 86400;
      if (OBIB_HOLD_MAX_DAYS > 0 and $holdAge > OBIB_HOLD_MAX_DAYS*$secondsPerDay) {
        $tooOld = true;
      }
      if (!$tooOld and $mbrid != $hold->getMbrid()) {
        // show error if member who placed hold is not current member
        $holdQ->close();
        $pageErrors["barcodeNmbr"] = $loc->getText("checkoutErr5",array("barcode"=>$barcode));
        $postVars["barcodeNmbr"] = $barcode;
        $_SESSION["postVars"] = $postVars;
        $_SESSION["pageErrors"] = $pageErrors;
        header("Location: ../circ/mbr_view.php?mbrid=".U($mbrid));
        exit();
      } else {
        // need to remove hold and continue on to checkout
        $holdQ->delete($hold->getBibid(),$hold->getCopyid(),$hold->getHoldid());
        if ($holdQ->errorOccurred()) {
          $holdQ->close();
          displayErrorPage($holdQ);
        }
        $holdQ->close();
      }
    }
    // need to reestablish copyQ connection so we can update status
    $copyQ->connect();
    if ($copyQ->errorOccurred()) {
      $copyQ->close();
      displayErrorPage($copyQ);
    }
  }

  #**************************************************************************
  #*  Update copy status code
  #**************************************************************************
  // we need to also insert into status history table
  $copy->setStatusCd(OBIB_STATUS_OUT);
  $copy->setMbrid($_POST["mbrid"]);
  if(isset($_POST['date_from']) && $_POST['date_from'] = 'override')
  list($today, $err) = Date::read_e("today");
  //$copy->setDueBackDt($dueDate);
  if (!$copyQ->update($copy,true)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->close();

  #**************************************************************************
  #*  Insert into biblio status history
  #**************************************************************************
  $hist = new BiblioStatusHist();
  $hist->setBibid($copy->getBibid());
  $hist->setCopyid($copy->getCopyid());
  $hist->setStatusCd($copy->getStatusCd());
  $hist->setStatusBeginDt($copy->getStatusBeginDt());
  $hist->setDueBackDt($copy->getDueBackDt());
  $hist->setMbrid($copy->getMbrid());
  $hist->setRenewalCount($copy->getRenewalCount());

  $histQ = new BiblioStatusHistQuery();
  $histQ->connect();
  if ($histQ->errorOccurred()) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  $histQ->insert($hist);
  if ($histQ->errorOccurred()) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  $histQ->close();
  
  #**************************************************************************
  #*  Update member's activity date
  #**************************************************************************
  $mbrQ->updateActivity($mbrid);
  $mbrQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  header("Location: ../user/user_view.php?mbrid=".U($mbrid));
?>

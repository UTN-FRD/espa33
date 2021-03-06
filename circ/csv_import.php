<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  require_once("../shared/common.php");
  require_once("../shared/global_constants.php");

  $tab = "circulation";
  $nav = "csv_import";

  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  include("../shared/header.php");
  include("../shared/logincheck.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE, $tab);

  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $filename = basename($_FILES['upload']['name']);
    $uploadfile = sys_get_temp_dir() . "/" . $filename;
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $uploadfile)) {
      $rows = array();
      $cols = array();
      $row_count = 1;
      if (($handle = fopen($uploadfile, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          $row = array();
          $num = count($data);
          for ($c = 0; $c < $num; $c++) {
            if ($row_count == 1) {
              // Header
              $cols[] = $data[$c];
            } else {
              $row[$cols[$c]] = $data[$c];
            }
          }
          if (count($row) > 0) {
            $rows[] = $row;
          }
          $row_count++;
        }
        fclose($handle);

        // Save to the database
        foreach ($rows as $row) {
          $member = new Member();
          $member->setMbrid($row['mbrid']);
          $member->setBarcodeNmbr($row['barcode_nmbr']);
          $member->setCreateDt($row['create_dt']);
          $member->setLastChangeDt($row['last_change_dt']);
          $member->setLastChangeUserid($row['last_change_userid']);
          $member->setLastName($row['last_name']);
          $member->setFirstName($row['first_name']);
          $member->setAddress($row['address']);
          $member->setCity($row['city']);
          $member->setHomePhone($row['home_phone']);
          $member->setWorkPhone($row['work_phone']);
          $member->setCel($row['cel']);
          $member->setEmail($row['email']);
          $member->setFoto($row['foto']);
          $member->setPassUser($row['pass_user']);
          $member->setBornDt($row['born_dt']);
          $member->setOther($row['other']);
          $member->setClassification($row['classification']);
          $member->setStatus($row['is_active']);
          $member->setLastActDate($row['last_activity_dt']);
          $member->setDni($row['dni']);
          $member->setLegajo($row['legajo']);

          $mbrQ = new MemberQuery();
          $mbrQ->connect();
          $dupBarcode = $mbrQ->DupBarcode($row['barcode_nmbr']);
          if (!$dupBarcode) {
            $mbrid = $mbrQ->insert($member);
            $mbrQ->updateActivity($mbrid);
            $mbrQ->close();
          }
        }
        echo $loc->getText("mbrImportCompleted");
      } else {
        echo $loc->getText("mbrCannotOpenFile");
      }
    } else {
      echo $loc->getText("mbrCannotUploadFile");
    }
  }
?>

<h3><?php echo $loc->getText("CSVImportHeader"); ?></h3>
  </br>
    <?php  echo $loc->getText("CSVImportSizeLimitNotes") . OBIB_MAX_FILE_SIZE . "  </br></br>";?>

<form method="post" enctype="multipart/form-data" action="<?php echo ".." . DOCUMENT_ROOT; ?>circ/csv_import.php">
  <div>
    <label for="upload"><?php echo $loc->getText("CSVLabel") . "  </br></br>"; ?></label>
  </div>
  <input type="file" name="<?php echo $loc->getText("upload"); ?>" class="btn btn-default" /><br>
  <input type="submit" name="submit" class="btn btn-primary" value="<?php echo $loc->getText("UploadFile"); ?>" />
</form>

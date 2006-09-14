<?php /* $Id: do_patients_fusion.php 140 2006-06-13 23:53:29Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 140 $
* @author Romain Ollivier
*/

global $AppUI, $m;

$medecin1 = new CMedecin;
$medecin1->load($_POST["medecin1_id"]);
$medecin2 = new CMedecin;
$medecin2->load($_POST["medecin2_id"]);

$do = new CDoObjectAddEdit("CMedecin", "medecin_id");
$do->doBind();

// Création du nouveau medecin
if (intval(dPgetParam($_POST, "del"))) {
  $do->doDelete();
} else {
  $do->doStore();
}

$medecin_id = $do->_obj->medecin_id;

// Régularisation des liens étrangers
$sql = "UPDATE `patients` SET" .
    "\n`medecin_traitant` = '$medecin_id'" .
    "\nWHERE `medecin_traitant` = '$medecin1->medecin_id'";
db_exec( $sql ); $msg = db_error();
$sql = "UPDATE `patients` SET" .
    "\n`medecin_traitant` = '$medecin_id'" .
    "\nWHERE `medecin_traitant` = '$medecin2->medecin_id'";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE `patients` SET" .
    "\n`medecin1` = '$medecin_id'" .
    "\nWHERE `medecin1` = '$medecin1->medecin_id'";
db_exec( $sql ); $msg .= db_error();$sql = "UPDATE `patients` SET" .
    "\n`medecin1` = '$medecin_id'" .
    "\nWHERE `medecin1` = '$medecin2->medecin_id'";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE `patients` SET" .
    "\n`medecin2` = '$medecin_id'" .
    "\nWHERE `medecin2` = '$medecin1->medecin_id'";
db_exec( $sql ); $msg .= db_error();$sql = "UPDATE `patients` SET" .
    "\n`medecin2` = '$medecin_id'" .
    "\nWHERE `medecin2` = '$medecin2->medecin_id'";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE `patients` SET" .
    "\n`medecin3` = '$medecin_id'" .
    "\nWHERE `medecin3` = '$medecin1->medecin_id'";
db_exec( $sql ); $msg .= db_error();$sql = "UPDATE `patients` SET" .
    "\n`medecin3` = '$medecin_id'" .
    "\nWHERE `medecin3` = '$medecin2->medecin_id'";
db_exec( $sql ); $msg .= db_error();

if($msg) {
  mbTrace($msg, "erreur sql", true);
  exit(0);
}

$medecin1->delete();
$medecin2->delete();

$do->doRedirect();

?>
<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;
$ds = CSQLDataSource::get("std");
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

$newMedecin =& $do->_obj;

// Transfert de toutes les backrefs
if ($msg = $newMedecin->transferBackRefsFrom($medecin1)) {
  $do->errorRedirect($msg);
}

if ($msg = $newMedecin->transferBackRefsFrom($medecin2)) {
  $do->errorRedirect($msg);
}

// Suppression des anciens objets
if ($msg = $medecin1->delete()) {
  $do->errorRedirect($msg);
}
  
if ($msg = $medecin2->delete()) {
  $do->errorRedirect($msg);
}

$medecin1->delete();
$medecin2->delete();

$do->doRedirect();

?>
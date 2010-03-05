<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLaboExamen", "prescription_labo_examen_id");

$pack = new CPackExamensLabo();
$pack->load($_POST["_pack_examens_labo_id"]);
$pack->loadRefs();

foreach($pack->_ref_items_examen_labo as $item) {
  $_POST["examen_labo_id"]       = $item->_ref_examen_labo->_id;
  $_POST["pack_examens_labo_id"] = $pack->_id;
  $do->doBind();
  $do->doStore();
}

$do->ajax = 1;
$do->doRedirect();

?>
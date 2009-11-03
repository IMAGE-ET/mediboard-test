<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

// Filtre des plages
$chir_id = $_POST['_old_chir_id'];
$where["chir_id"] = "= '$chir_id'";

if ($date_min = CValue::post("_date_min")) {
  $where[] = "date >= '$date_min'";
}

if ($date_max = CValue::post("_date_max")) {
  $where[] = "date <= '$date_max'";
}

// Chargement des plages
$plage = new CPlageconsult();
$plages = $plage->loadList($where);

foreach ($plages as $_plage) {
  $_POST["plageconsult_id"] = $_plage->_id;
  
  // Calcul de collisions
  $_POST["date"] = $_plage->date;

  // Do it !
  $do = new CDoObjectAddEdit("CPlageconsult", "plageconsult_id");
	$do->redirect = null;
  $do->doIt();
}

// Redirection finale
$do->redirect = "m=$m&a=transfert_plageconsult&dialog=1";
$do->doRedirect();
?>
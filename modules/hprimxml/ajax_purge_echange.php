<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$do_purge = CValue::get("do_purge");
$date_min = CValue::get("date_min");
$date_max = CValue::get("date_max");

if (!$date_min || !$date_max) {
	CAppUI::stepAjax("Merci d'indiquer une date de début et de fin de recherche.", UI_MSG_ERROR);
}

// Filtre sur les enregistrements
$itemEchangeHprim = new CEchangeHprim;

// Requêtes
$where = array();
$where["date_production"] = "BETWEEN '".$date_min."' AND '".$date_max."'";
$where["purge"] = "= '0'";

if (!$do_purge) {
  $count = $itemEchangeHprim->countList($where);
  CAppUI::stepAjax($count." échanges H'XML à purger");
} else {
	$order = "date_production ASC";
	
  // Récupération de la liste des echanges HPRIM
  $echangesHprim = $itemEchangeHprim->loadList($where, $order, "0, 100");
  $count  = 0;
  foreach($echangesHprim as $_echange_hprim) {  
    // Suppression du champ message et acquittement de l'échange
    $_echange_hprim->_message = "";
		$_echange_hprim->_acquittement = "";
		
    $_echange_hprim->purge = 1;
    if ($msg = $_echange_hprim->store()) {
      CAppUI::stepAjax("#$_echange_hprim->_id : Impossible de sauvegarder l'échange H'XML", UI_MSG_WARNING);
      CAppUI::stepAjax($msg, UI_MSG_WARNING);
      continue;
    }
    $count++;
  }
  if ($count == 0) {
    echo "<script type='text/javascript'>stop=true;</script>";
  }
  CAppUI::stepAjax("$count échanges H'XML purgés");
}

?>
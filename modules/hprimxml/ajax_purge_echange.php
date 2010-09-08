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
$ljoin = "";
$ljoin["content_xml"] = "`content_xml`.`content_id` = `echange_hprim`.`message_content_id`";
    
$where = array();
$where["echange_hprim.date_production"] = "BETWEEN '".$date_min."' AND '".$date_max."'";
$where["content_xml.content"] = "IS NOT NULL";

if (!$do_purge) {
  $count = $itemEchangeHprim->countList($where, null, null, null, $ljoin);
  CAppUI::stepAjax($count." échanges H'XML à purger");
} else {
	$order = "date_production ASC";
	
  // Récupération de la liste des echanges HPRIM
  $echangesHprim = $itemEchangeHprim->loadList($where, $order, "0, 500", null, $ljoin);
  $count  = 0;
  foreach($echangesHprim as $_echange_hprim) {  
    $_echange_hprim->loadContent();
    
    // Suppression du champ message et acquittement de l'échange
    $_echange_hprim->_message      = "";
		$_echange_hprim->_acquittement = "";

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
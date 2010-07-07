<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
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
$echange_soap = new CEchangeSOAP();

// Requêtes
$where = array();
$where["date_echange"] = "BETWEEN '".$date_min."' AND '".$date_max."'";
$where["purge"] = "= '0'";

if (!$do_purge) {
  $count = $echange_soap->countList($where);
  CAppUI::stepAjax($count." échanges SOAP à purger");
} else {
  $order = "date_echange ASC";
  
  // Récupération de la liste des echanges SOAP
  $echangesSoap = $echange_soap->loadList($where, $order, "0, 100");
  $count  = 0;
  foreach($echangesSoap as $_echange_soap) {  
    // Suppression du champ input et output
    $_echange_soap->input = "";
    $_echange_soap->output = "";
    
    $_echange_soap->purge = 1;
    if ($msg = $_echange_soap->store()) {
      CAppUI::stepAjax("#$_echange_soap->_id : Impossible de sauvegarder l'échange SOAP", UI_MSG_WARNING);
      CAppUI::stepAjax($msg, UI_MSG_WARNING);
      continue;
    }
    $count++;
  }
  if ($count == 0) {
    echo "<script type='text/javascript'>stop=true;</script>";
  }
  CAppUI::stepAjax("$count échanges SOAP purgés");
}

?>
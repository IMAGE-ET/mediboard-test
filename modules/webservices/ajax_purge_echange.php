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
$max = CValue::get("max", 10000);

if (!$date_min || !$date_max) {
  CAppUI::stepAjax("Merci d'indiquer une date de début et de fin de recherche.", UI_MSG_ERROR);
}

// Filtre sur les enregistrements
$echange_soap = new CEchangeSOAP();

// Requêtes
$where = array();
$where["date_echange"] = "BETWEEN '$date_min' AND '$date_max'";
$where["purge"] = "= '0'";

if (!$do_purge) {
  $count = $echange_soap->countList($where);
  CAppUI::stepAjax($count." échanges SOAP à purger");
} else {  
  // Suppression du champ input et output
  $echange_soap->_spec->ds->query("UPDATE `echange_soap` 
                                   SET `purge` = '1', `input` = '', `output` = ''
                                   WHERE `date_echange` BETWEEN '$date_min' AND '$date_max'
                                   AND `purge` = '0'
                                   LIMIT $max;");
  
  $count = $echange_soap->_spec->ds->affectedRows();
  
  if ($count == 0) {
    echo "<script type='text/javascript'>stop=true;</script>";
  }
  CAppUI::stepAjax("$count échanges SOAP purgés - ".mbDateTime());
}

?>

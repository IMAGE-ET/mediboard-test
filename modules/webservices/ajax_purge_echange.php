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
$date_max = CValue::get("date_max");
$months   = CValue::get("months");
$max      = CValue::get("max", 1000);

// Use months syntax
if ($months) {
  $date_max = mbDate("- $months MONTHS");
}

// Check params
if (!$date_max) {
  CAppUI::stepAjax("Merci d'indiquer une date fin de recherche.", UI_MSG_ERROR);
}

// Filtre sur les enregistrements
$echange_soap = new CEchangeSOAP();

// Requête
$where = array();
$where["date_echange"] = "< '$date_max'";
$where["purge"] = "= '0'";

// Dry run
$count = $echange_soap->countList($where);
CAppUI::stepAjax("CEchangeSOAP-msg-purge_count", UI_MSG_OK, $count);
if (!$do_purge) {
  return;
}

// Suppression du champ input et output
$query = "UPDATE `echange_soap` 
  SET `purge` = '1', `input` = '', `output` = ''
  WHERE `date_echange` < '$date_max'
  AND `purge` = '0'
  LIMIT $max";

// Comptage
$ds = $echange_soap->_spec->ds;
$ds->exec($query);
$count = $ds->affectedRows();
if ($count) {
  echo "<script type='text/javascript'>Echange.purge();</script>";
}

CAppUI::stepAjax("CEchangeSOAP-msg-purged_count", UI_MSG_OK, $count);
?>

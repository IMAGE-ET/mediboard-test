<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ftp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

// Ce fichier est identique à celui du module webservices
// @todo factoriser

CCanDo::checkAdmin();

$do_purge = CValue::get("do_purge");
$date_max = CValue::get("date_max");
$months   = CValue::get("months");
$max      = CValue::get("max", 1000);
$delete   = CValue::get("delete");

if ($months) {
  $date_max = CMbDT::date("- $months MONTHS");
}

if (!$date_max) {
  CAppUI::stepAjax("Merci d'indiquer une date fin de recherche.", UI_MSG_ERROR);
}

$exchange_class = "CExchangeFTP";
$exchange = new $exchange_class;
$ds = $exchange->_spec->ds;

// comptage des echanges à supprimer
$count_delete = 0;
$date_max_delete = CMbDT::date("-6 MONTHS", $date_max);

if ($delete) {
  $where = array();
  $where["date_echange"] = "< '$date_max_delete'";
  $count_delete = $exchange->countList($where);

  CAppUI::stepAjax("$exchange_class-msg-delete_count", UI_MSG_OK, $count_delete);
}

// comptage des echanges à vider qui ne le sont pas deja
$where = array();
$where["date_echange"] = "< '$date_max'";
$where["purge"] = "= '0'";
$count_purge = $exchange->countList($where);

CAppUI::stepAjax("$exchange_class-msg-purge_count", UI_MSG_OK, $count_purge);

if (!$do_purge) {
  return;
}

// suppression effective
if ($delete) {
  $query = "DELETE FROM `{$exchange->_spec->table}` 
    WHERE `date_echange` < '$date_max_delete'
    LIMIT $max";

  $ds->exec($query);
  $count_delete = $ds->affectedRows();
  CAppUI::stepAjax("$exchange_class-msg-deleted_count", UI_MSG_OK, $count_delete);
}

// vidage des champs effective
$query = "UPDATE `{$exchange->_spec->table}` 
  SET 
  `purge` = '1', 
  `output` = '', 
  `input` = ''
  WHERE `date_echange` < '$date_max'
  AND `purge` = '0'
  LIMIT $max";

$ds->exec($query);
$count_purge = $ds->affectedRows();
CAppUI::stepAjax("$exchange_class-msg-purged_count", UI_MSG_OK, $count_purge);

// on continue si on est en auto
if ($count_purge + $count_delete) {
  echo "<script type='text/javascript'>Echange.purge();</script>";
}

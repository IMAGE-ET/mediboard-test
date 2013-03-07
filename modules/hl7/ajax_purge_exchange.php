<?php

/**
 * Delete exchange HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$do_purge = CValue::get("do_purge");
$date_max = CValue::get("date_max");
$months   = CValue::get("months", 1);
$max      = CValue::get("max"   , 1000);

// Use months syntax
if ($months) {
  $date_max = CMbDT::date("- $months MONTHS");
}

// Check params
if (!$date_max) {
  CAppUI::stepAjax("Merci d'indiquer une date fin de recherche.", UI_MSG_ERROR);
}

$exchange_ihe = new CExchangeIHE();

// Dry run
if (!$do_purge) {
  $where = array();
  $where["exchange_ihe.date_production"]         = "< '$date_max'";
  $where["exchange_ihe.message_content_id"]      = "IS NOT NULL";
  $where["exchange_ihe.acquittement_content_id"] = "IS NOT NULL";
  $count = $exchange_ihe->countList($where);
  
  CAppUI::stepAjax("CExchangeIHE-msg-purge_count", UI_MSG_OK, $count);
  return;
}

// Filtre sur les enregistrements
$content_tabular = new CContentTabular();

$count = 0;
$count += deleteContentAndUpdateExchange($content_tabular, "message_content_id"     , $date_max, $max);
$count += deleteContentAndUpdateExchange($content_tabular, "acquittement_content_id", $date_max, $max);

if ($count) {
  echo "<script type='text/javascript'>Echange.purge();</script>";
}

CAppUI::stepAjax("CExchangeIHE-msg-purged_count", UI_MSG_OK, $count, CMbDT::dateTime());

/**
 * Delete content and update exchange
 *
 * @param CContentTabular $content_tabular Content tabular
 * @param int             $type_content_id Content ID
 * @param date            $date_max        Date max
 * @param int             $max             Max exchange
 *
 * @return int
 */
function deleteContentAndUpdateExchange(CContentTabular $content_tabular, $type_content_id, $date_max, $max) {
  $ds = $content_tabular->_spec->ds;

  // Récupère les content Tabulé
  $query = "SELECT cx.content_id
            FROM content_tabular AS cx, exchange_ihe AS ec
            WHERE ec.`date_production` < '$date_max'
            AND ec.$type_content_id = cx.content_id
            LIMIT $max;";
  $ids = CMbArray::pluck($ds->loadList($query), "content_id");
  
  // Suppression du contenu Tabulé
  $query = "DELETE FROM content_tabular
            WHERE content_id ".CSQLDataSource::prepareIn($ids);
  $ds->exec($query);
  
  // Mise à jour des échanges
  $query = "UPDATE exchange_ihe
              SET `$type_content_id` = NULL 
              WHERE `$type_content_id` ".CSQLDataSource::prepareIn($ids);
  $ds->exec($query);  
  $count = $ds->affectedRows();

  return $count;
}
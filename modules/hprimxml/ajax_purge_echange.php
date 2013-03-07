<?php

/**
 * Delete exchange H'XML
 *
 * @category HprimXML
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

// Dry run
if (!$do_purge) {
  $where["echange_hprim.date_production"]         = "< '$date_max'";
  $where["echange_hprim.message_content_id"]      = "IS NOT NULL";
  $where["echange_hprim.acquittement_content_id"] = "IS NOT NULL";
  $count = $content_xml->countList($where, null, $ljoin);
  
  CAppUI::stepAjax("CEchangeHprim-msg-purge_count", UI_MSG_OK, $count);
  return;
}


// Filtre sur les enregistrements
$content_xml = new CContentXML();

$count = 0;
$count += deleteContentAndUpdateExchange($content_xml, "message_content_id", $date_max, $max);
$count += deleteContentAndUpdateExchange($content_xml, "acquittement_content_id", $date_max, $max);

if ($count) {
  echo "<script type='text/javascript'>Echange.purge();</script>";
}

CAppUI::stepAjax("CEchangeHprim-msg-purged_count", UI_MSG_OK, $count, CMbDT::dateTime());

/**
 * Delete content and update exchange
 *
 * @param CContentXML $content_xml     Content tabular
 * @param int         $type_content_id Content ID
 * @param date        $date_max        Date max
 * @param int         $max             Max exchange
 *
 * @return int
 */
function deleteContentAndUpdateExchange(CContentXML $content_xml, $type_content_id, $date_max, $max) {
  $ds = $content_xml->_spec->ds;

  // Récupère les content XML 
  $query = "SELECT cx.content_id
            FROM content_xml AS cx, echange_hprim AS ec 
            WHERE ec.`date_production` < '$date_max'
            AND ec.$type_content_id = cx.content_id
            LIMIT $max;";
  $ids = CMbArray::pluck($ds->loadList($query), "content_id");
  
  // Suppression du contenu XML
  $query = "DELETE FROM content_xml
            WHERE content_id ".CSQLDataSource::prepareIn($ids);
  $ds->exec($query);
  
  // Mise à jour des échanges
  $query = "UPDATE echange_hprim
              SET `$type_content_id` = NULL 
              WHERE `$type_content_id` ".CSQLDataSource::prepareIn($ids);
  $ds->exec($query);  
  $count = $ds->affectedRows();

  return $count;
}
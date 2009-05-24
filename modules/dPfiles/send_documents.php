<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsEdit();

$max_sent = mbGetValueFromGet("max_sent", 10);
$max_loaded = mbGetValueFromGet("max_loaded", 1000);
$do = mbGetValueFromGet("do", "0");

// Auto send categories
$category = new CFilesCategory();
$category->send_auto = "1";
foreach ($categories = $category->loadMatchingList() as $_category) {
  $_category->countDocItems();
  $_category->countUnsentDocItems();
}

// Unsent docItems
$where["file_category_id"] = CSQLDataSource::prepareIn(array_keys($categories));
$where["etat_envoi"      ] = "!= 'oui'";
$where["object_id"       ] = "IS NOT NULL";

$file = new CFile();
$items["CFile"] = $file->loadList($where, "file_id DESC", $max_sent);
foreach ($items["CFile"] as $_file) {
  $_file->loadTargetObject();
  if ($do && !$_file->_send_problem) {
    $_file->_send = "1";
    $_file->_send_problem = $_file->store();
  }
}

$document = new CCompteRendu();
$items["CCompteRendu"] = $document->loadList($where, "compte_rendu_id DESC", $max_sent);
foreach ($items["CCompteRendu"] as $_document) {
  $_file->loadTargetObject();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories", $categories);
$smarty->assign("items", $items);

$smarty->display("send_documents.tpl");
?>
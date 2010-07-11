<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsEdit();

set_time_limit(90);

CRecordSante400::$verbose = CValue::get("verbose");

$types = CMouvFactory::getTypes();
$type = CValue::getOrSession("type", reset($types));

$marked = CValue::getOrSession("marked", "1");
$max = CValue::get("max", CAppUI::conf("dPsante400 nb_rows"));

// Load mouvements
$class = CValue::get("class");
$mouv =  $class ? new $class : CMouvFactory::create($type);
$count = $mouv->count($marked);
$mouvs = array();
if ($rec = CValue::get("rec")) {
  try {
    $mouv->load($rec);
    $mouvs = array($mouv);
  } catch (Exception $e) {
    trigger_error("Mouvement with id '$rec'has been deleted : " . $e->getMessage(), E_USER_ERROR);
  }
} else {
  $mouvs = $mouv->loadList($marked, $max);
}


// Proceed mouvements
$procs = 0;
foreach ($mouvs as $_mouv) {
  if ($_mouv->proceed()) {
    $procs++;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("connection", CRecordSante400::$dbh);
$smarty->assign("types", $types);
$smarty->assign("type", $type);
$smarty->assign("marked", $marked);
$smarty->assign("count", $count);
$smarty->assign("procs", $procs);
$smarty->assign("mouvs", $mouvs);
$smarty->display("synchro_sante400.tpl");

?>

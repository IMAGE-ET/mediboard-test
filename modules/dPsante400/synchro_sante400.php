<?php /** $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

CApp::setTimeLimit(90);

CRecordSante400::$verbose = CValue::get("verbose");

$types = CMouvFactory::getTypes();
if (!count($types)) {
  CAppUI::stepMessage(UI_MSG_WARNING, "CMouvFactory-warning-noclasses");
  return;
}

$marked = CValue::getOrSession("marked", "1");
$max = CValue::get("max", CAppUI::conf("sante400 nb_rows"));

// Load mouvements
$class = CValue::get("class");
$type = CValue::getOrSession("type");
$mouvs = array();
$count = 0;
$procs = 0;

if (!in_array($type, $types)) {
  $type = null;
}

// Mouvement type (or class) provided
if ($type || $class) {
  // Mouvement construction by factory
  $mouv =  $class ? new $class : CMouvFactory::create($type);

  if (!$mouv) {
    CAppUI::stepMessage(UI_MSG_ERROR, "CMouvFactory-error-noclass", CValue::first($type, $class));
    return;
  }

  // Initialisation d'un fichier de verrou
  $class = $mouv->class;
  $lock = new CMbLock("synchro_sante400/{$class}");
  
  // Mouvements counting
  $count = $mouv->count($marked);

  // Mouvements loading
  /** @var CMouvement400[] $mouvs */
  $mouvs = array();
  if ($rec = CValue::get("rec")) {
    try {
      $mouv->load($rec);
      $mouvs = array($mouv);
    } 
    catch (Exception $e) {
      trigger_error("Mouvement with id '$rec' has been deleted : " . $e->getMessage(), E_USER_ERROR);
    }
  }
  else {
    // On tente de verrouiller seuement pour les traitements de masse
    if (!$lock->acquire()) {
      $lock->failedMessage();
      return;
    }

    $mouvs = $mouv->loadList($marked, $max);
  }
  
  // Proceed mouvements
  foreach ($mouvs as $_mouv) {
    if ($_mouv->proceed()) {
      $procs++;
    }
  }

  $lock->release();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("connection", CRecordSante400::$dbh);
$smarty->assign("type", $type);
$smarty->assign("class", $class);
$smarty->assign("types", $types);
$smarty->assign("marked", $marked);
$smarty->assign("count", $count);
$smarty->assign("procs", $procs);
$smarty->assign("mouvs", $mouvs);
$smarty->assign("relaunch", CValue::get("relaunch"));

$smarty->display("synchro_sante400.tpl");

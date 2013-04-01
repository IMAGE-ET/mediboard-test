<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


CCanDo::checkAdmin();

$marked = CValue::get("marked");
$action = CValue::get("action");

$type = CValue::get("type");
$types = $type == "all" ? CMouvFactory::getTypes() : array($type);

$marked = CValue::get("marked");
$marked = $marked == "all" ? array("0", "1") : array($marked);

foreach ($types as $_type) {
  $mouv = CMouvFactory::create($_type);
	if ($action == "count") {
    $mouv->loadLatest();
    CAppUI::stepAjax("Latest available trigger for type '%s' is '%s' dating '%s'", UI_MSG_OK, 
      $_type,
      $mouv->rec, 
      $mouv->when);
      
	  foreach ($marked as $_marked) {
	    $count = $mouv->count($_marked);
	    CAppUI::stepAjax("%s - %s : %s disponibles ", UI_MSG_OK, 
	      CAppUI::tr("CMouvement400-type-$_type"),
	      CAppUI::tr("CMouvement400-marked-$_marked"), 
	      $count);
    }
	}
	
  if ($action == "obsolete") {
    $mouv->loadOldest();
    CAppUI::stepAjax("Oldest available trigger for type '%s' is '%s' dating '%s'", UI_MSG_OK, 
		  $_type,
			$mouv->rec, 
			$mouv->when);
			
		$count = $mouv->count(true, $mouv->rec);
    CAppUI::stepAjax("Counting '%s' obsolete marked triggers", $count ? UI_MSG_WARNING : UI_MSG_OK, 
      $count);
    
		$max = CAppUI::conf("dPsante400 nb_rows");
    $count = $mouv->markObsoleteTriggers($mouv->rec, $max);
		if (is_string($count)) {
      CAppUI::stepAjax("Error marking obsolete trigger: %s", UI_MSG_WARNING, $count);
		}
		else {
      CAppUI::stepAjax("Marked '%s' obsolete triggers", UI_MSG_OK, $count);
		}
  }	
}

?>
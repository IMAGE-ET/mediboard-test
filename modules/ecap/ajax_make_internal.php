<?php /* $Id: export_documents.php 6141 2009-04-21 14:19:23Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: 6141 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

class CTriggerMarkImporter {
  static $creations = 0;
  static $updations = 0;
  
	static function storeMark(CMouvement400 $mouv) {
	  $mark = $mouv->loadTriggerMark();
	  $step = $mark->_id ? self::$updations++ : self::$creations; 
	  if ($msg = $mark->store()) {
	    CAppUI::stepAjax( "Mark store failed : %s", UI_MSG_WARNING, $msg);
	  }
	}
	
	static function importAll() {
		foreach (CMouvFactory::getTypes() as $_type) {
			// Initialise count
		  self::$creations = 0; 
			self::$updations = 0;
		  
			// Import latest success
			$mouv = CMouvFactory::create($_type);
		  $mouv->loadLatestSuccessWithFormerMark();
		  CAppUI::stepAjax("Latest sucess mark for class '%s' is '%s'", UI_MSG_OK, get_class($mouv), $mouv->rec);
		  self::storeMark($mouv);
		  
		  // Import marked triggers
      $max = CValue::getOrSession("max", 100);
		  $mouvs = $mouv->loadListWithFormerMark($max);
		  CAppUI::stepAjax("Marked triggers count for class '%s' is '%s'", UI_MSG_OK, get_class($mouv), count($mouvs));
		  foreach ($mouvs as $_mouv) {
		    self::storeMark($_mouv);
		  }
		  
		  // Report
		  CAppUI::stepAjax("Trigger marks creations '%s'", UI_MSG_OK, self::$creations);
		  CAppUI::stepAjax("Trigger marks updations '%s'", UI_MSG_OK, self::$updations);
		}
	}
}

CTriggerMarkImporter::importAll();
?>
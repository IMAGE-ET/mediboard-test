<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("ecap", "mouvsejourecap");

class CMouvAttendueECap extends CMouvSejourEcap {  
  
  function __construct() {
    parent::__construct();
    $this->table = "TRATT";
  }

  function synchronize() {
    $this->syncEtablissement();
    $this->syncFonction();
    $this->syncSalle();
    
    // Praticien du séjour si aucune DHE
    $this->syncPatient();
    
//    $this->syncSej();
    $this->syncDHE();
    $this->syncOperations();
    $this->syncNaissance();
  }
      
  function syncDHE() {

    $tags[] = "eCap";
		$tags[] = "DHE";
		$tags[] = "CIDC:{$this->id400Etab->id400}";
		
    self::$consumeUnsets = false;
    $IDAT = $this->consume("IDAT");
    self::$consumeUnsets = true;

    $this->id400DHE = new CIdSante400();
    $this->id400DHE->id400 = $IDAT;
    $this->id400DHE->object_class = "CSejour";
    $this->id400DHE->tag = join(" ", $tags);

    $this->sejour = $this->id400DHE->getCachedObject(0);
    $this->sejour->annule = $this->type == "S" ? '1' : '0';

    // Recherche de la DHE
    $this->mapDHE($this);

    $this->trace($this->sejour->getDBFields(), "Séjour (via DHE) à enregistrer");

    $this->sejour->_check_bounds = false;
    $this->id400DHE->bindObject($this->sejour);

    $this->markStatus(self::STATUS_SEJOUR);
  }
  
  function syncOperations() {
    parent::syncOperations();
  }

  function syncActes($CINT) {
    parent::syncActes($CINT);
  }

  function syncSej() {
    
    parent::syncSej();
  }
}
?>

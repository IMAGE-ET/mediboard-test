<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("dPsante400", "mouvsejourecap");

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
//    $this->syncDHE();
//    $this->syncOperations();
//    $this->syncNaissance();
  }
      
  function syncDHE() {
    $tag = "eCap DHE CIDC:{$this->id400Etab->id400}";
    $this->id400DHE = new CIdSante400();
    $this->id400DHE->id400 = $IDAT;
    $this->id400DHE->tag = $tag;

    $this->trace($this->sejour->getDBFields(), "Séjour (via DHE) à enregistrer");

    // Recherche de la DHE
    $this->mapDHE($dheECap);

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
    // Pas de NDOS, créer un séjour temporaire
    if (!$this->data["NDOS"]) {
      $this->sejour = new CSejour();
      return;
    }
    
    parent::syncSej();
  }
}
?>

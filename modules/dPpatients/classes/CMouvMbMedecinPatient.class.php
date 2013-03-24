<?php /* $Id: mouvattendueecap.class.php 9406 2010-07-09 15:47:39Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: 9406 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Handle patient syncing with medecin table
 */
class CMouvMbMedecinPatient extends CMouvement400 {  
  
  function __construct() {
    parent::__construct();
    $this->base = CAppUI::conf("sante400 dsn");
    $this->table = "medecin_trigger";
    $this->idField   = "trigger_id";
    $this->typeField = "type";
    $this->keyField  = "medecin_id";
  }

  function initialize() {
    parent::initialize();

    $this->when = $this->consume("datetime");
    $this->valuePrefix = $this->type == "delete" ? "old_": "new_";

    // Analyse changed fields
    foreach (array_keys($this->data) as $beforeName) {
      $matches = array();
      if (!preg_match("/old_(\w*)/i", $beforeName, $matches)) {
        continue;
      }
      
      $name = $matches[1];
      $afterName = "new_$name";
      if ($this->data[$beforeName] != $this->data[$afterName]) {
        $this->changedFields[] = $name;
      }
    }
  }

  function synchronize() {    
    $this->syncPatient();

    $this->starStatus(self::STATUS_ETABLISSEMENT);
    $this->starStatus(self::STATUS_FONCSALLSERV);
    $this->starStatus(self::STATUS_PRATICIEN);
    $this->starStatus(self::STATUS_SEJOUR);
    $this->starStatus(self::STATUS_OPERATION);
    $this->starStatus(self::STATUS_PRATICIEN);
    $this->starStatus(self::STATUS_ACTES);
    $this->starStatus(self::STATUS_NAISSANCE);
  }
  
  function syncPatient($update = true) {
    $medecin_id = $this->consume("medecin_id");
    
    // Gestion des id400
    $tag = "medecin-patient";
    $this->idexPat = new CIdSante400();
    $this->idexPat->object_class = "CPatient";
    $this->idexPat->id400 = $medecin_id;
    $this->idexPat->tag = $tag;
    
    // Identité
    $this->patient = new CPatient;
    $this->patient->nom       = $this->consume("nom");
    $this->patient->prenom    = $this->consume("prenom");
    
    // Simulation de l'âge
    $year = 1980 - strlen($this->patient->nom);
    $month = '01';
    $day = str_pad(strlen($this->patient->prenom) % 30, 2, '0');
    $this->patient->naissance = "$year-$month-$day";
       
    // Binding
    $this->trace($this->patient->getPlainFields(), "Patient à enregistrer");
    $this->idexPat->bindObject($this->patient);

    $this->markStatus(self::STATUS_PATIENT);
  }
  
}
?>

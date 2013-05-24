<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * The HPRIM 2.1 sejour class
 */
class CHprim21Sejour extends CHprim21Object {
  // DB Table key
	public $hprim21_sejour_id;
  
  // DB references
  public $hprim21_patient_id;
  public $hprim21_medecin_id;
  public $sejour_id;
	
  // DB Fields
  public $date_mouvement;
  public $statut_admission;
  public $localisation_lit;
  public $localisation_chambre;
  public $localisation_service;
  public $localisation4;
  public $localisation5;
  public $localisation6;
  public $localisation7;
  public $localisation8;
  
  public $_ref_sejour;
  public $_ref_hprim21_medecin;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hprim21_sejour';
    $spec->key   = 'hprim21_sejour_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "hprim21_patient_id"   => "ref notNull class|CHprim21Patient",
      "hprim21_medecin_id"   => "ref class|CHprim21Medecin",
      "sejour_id"            => "ref class|CSejour",
      "date_mouvement"       => "dateTime",
      "statut_admission"     => "enum list|OP|IP|IO|ER|MP|PA",
      "localisation_lit"     => "str",
      "localisation_chambre" => "str",
      "localisation_service" => "str",
      "localisation4"        => "str",
      "localisation5"        => "str",
      "localisation6"        => "str",
      "localisation7"        => "str",
      "localisation8"        => "str",
    );
    return array_merge($specsParent, $specs);
  }
  
  function bindToLine($line, &$reader, $patient, $medecin = null) {
    $this->setHprim21ReaderVars($reader);
    $this->hprim21_patient_id   = $patient->_id;
    if($medecin) {
      $this->hprim21_medecin_id   = $medecin->_id;
    }
    
    $elements                   = explode($reader->separateur_champ, $line);
  
    if(count($elements) < 34) {
      $reader->error_log[] = "Champs manquant dans le segment patient (sejour) : ".count($elements)." champs trouvés";
      return false;
    }
    if(!$elements[4]) {
      //$reader->error_log[] = "Identifiant externe manquant dans le segment patient (sejour)";
      return true;
    }
    
    $this->external_id          = $elements[4];
    $this->loadMatchingObject();
    $this->date_mouvement       = $this->getDateTimeFromHprim($elements[23]);
    $this->statut_admission     = $elements[24];
    $localisation               = explode($reader->separateur_sous_champ, $elements[25]);
    $this->localisation_lit     = $localisation[0];
    $this->localisation_chambre = $localisation[1];
    $this->localisation_service = $localisation[2];
    if(isset($localisation[3])) {
      $this->localisation4      = $localisation[3];
    }
    if(isset($localisation[4])) {
      $this->localisation5      = $localisation[4];
    }
    if(isset($localisation[5])) {
      $this->localisation6      = $localisation[5];
    }
    if(isset($localisation[6])) {
      $this->localisation7      = $localisation[6];
    }
    if(isset($localisation[7])) {
      $this->localisation8      = $localisation[7];
    }
    return true;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    $this->_view = "Séjour du ".CMbDT::transform(null, $this->date_mouvement, "%d/%m/%Y")." [".$this->external_id."]";
  }
  
  function loadRefHprim21Medecin(){
    $this->_ref_hprim21_medecin = new CHprim21Medecin();
    $this->_ref_hprim21_medecin->load($this->hprim21_medecin_id);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    // Chargement du séjour correspondant
    $this->_ref_sejour = new CSejour();
    $this->_ref_sejour->load($this->sejour_id);
    $this->loadRefHprim21Medecin();
  }
}

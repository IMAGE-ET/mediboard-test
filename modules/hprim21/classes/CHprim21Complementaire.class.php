<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * The HPRIM 2.1 assurance complémentaire class
 */
class CHprim21Complementaire extends CHprim21Object {
  // DB Table key
	public $hprim21_complementaire_id;
  
  // DB references
  public $hprim21_patient_id;
	
  // DB Fields
  public $code_organisme;
  public $numero_adherent;
  public $debut_droits;
  public $fin_droits;
  public $type_contrat;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hprim21_complementaire';
    $spec->key   = 'hprim21_complementaire_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "hprim21_patient_id" => "ref class|CHprim21Patient",
      "code_organisme"     => "str",
      "numero_adherent"    => "str",
      "debut_droits"       => "date",
      "fin_droits"         => "date",
      "type_contrat"       => "str",
    );
    return array_merge($specsParent, $specs);
  }
  
  function bindToLine($line, &$reader, $patient) {
    $this->setHprim21ReaderVars($reader);
    $this->hprim21_patient_id = $patient->_id;
    
    $elements                 = explode($reader->separateur_champ, $line);
  
    if(count($elements) < 7) {
      $reader->error_log[] = "Champs manquant dans le segment assurance complémentaire";
      return false;
    }
    if(!$elements[2]) {
      $reader->erreo_log[] = "Identifiant externe dans le segment assurance complémentaire";
    }
    
    $this->external_id        = $patient->external_id.$elements[2];
    $this->loadMatchingObject();
    $this->code_organisme     = $elements[2];
    $this->numero_adherent    = $elements[3];
    $this->debut_droits       = $this->getDateFromHprim($elements[4]);
    $this->fin_droits         = $this->getDateFromHprim($elements[5]);
    $this->type_contrat       = $elements[6];
    
    return true;
  }
}

<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Modulateur d'activités CsARR
 */
class CModulateurCsARR extends CCsARRObject {

  // DB Fields
  public $code       = null;
  public $modulateur = null;

  // Derived Fields
  public $_libelle;
    
  public $_ref_code = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'modulateur';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"       ] = "str notNull length|7";
    $props["modulateur" ] = "str notNull length|2";

    // Plain Fields
    $props["_libelle"] = "str";

    return $props;
  }

  /**
   * Override
   *
   * @see parent::updateFormFields()
   * @return void
   */
  function updateFormFields() {
    static $libelles = array(
      "ZV" => "Réalisation de l'acte au lit du patient",
      "ME" => "Réalisation de l'acte en salle de soins",
      "QM" => "Réalisation de l'acte en piscine ou en balnéotherapie",
      "TF" => "Réalisation de l'acte en établissement, en extérieur sans équipement",
      "RW" => "Réalisation de l'acte en établissement, en extérieur avec équipement",
      "HW" => "Réalisation de l'acte hors établissement en milieu urbain",
      "LJ" => "Réalisation de l'acte hors établissement en milieu naturel",
      "XH" => "Réalisation de l'acte sur le lieu de vie du patient",
      "BN" => "Nécessité de recours à un interprète",
      "EZ" => "Réalisation fractionnée de l'acte",
    );
    
    parent::updateFormFields();
    $this->_libelle = $libelles[$this->modulateur];
    $this->_view = "$this->modulateur: $this->_libelle";
  }

  /**
   * Chargement du détail d'activité CsARR
   *
   * @return CActiviteCsARR
   */
  function loadRefCode() {
    return $this->_ref_code = CActiviteCsARR::get($this->code);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefCode();
  }
}

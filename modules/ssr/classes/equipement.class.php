<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Equipement de SSR, fait parti d'un plateau technique
 */
class CEquipement extends CMbObject {
  // DB Table key
  var $equipement_id = null;
  
  // References
  var $plateau_id = null;

  // DB Fields
  var $nom          = null;
  var $visualisable = null;
  var $actif        = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'equipement';
    $spec->key   = 'equipement_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["plateau_id"]   = "ref notNull class|CPlateauTechnique";
    $props["nom"]          = "str notNull";
    $props["visualisable"] = "bool notNull default|1";
    $props["actif"]        = "bool notNull default|1";
    return $props;
  }
	
	 function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["evenements_ssr"]  = "CEvenementSSR equipement_id";
    return $backProps;
  }
	
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}

?>
<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CCellSaver extends CMbObject {
	 //DB Table Key
	var $cell_saver_id = null;
	
	//DB Fields 
	var $marque = null;
	var $modele = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cell_saver';
    $spec->key   = 'cell_saver_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["marque"] = "str notNull maxLength|50";
    $specs["modele"] = "str notNull maxLength|50";
    return $specs;
  }
  
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["blood_salvages"] = "CBloodSalvage cell_saver_id";
	  return $backProps;
	}	
	
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->marque $this->modele" ;
  }
}
?>
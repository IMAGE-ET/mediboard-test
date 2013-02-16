<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Note concernant une activite CsARR
 */
class CNoteActiviteCsARR extends CCsARRObject {
  
  var $code       = null;
  var $idnote     = null;
  var $typenote   = null;
  var $niveau     = null;
  var $libelle    = null;
  var $ordre      = null;
  var $code_exclu = null;
  
  var $_ref_code       = null;
  var $_ref_code_exclu = null;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'note_activite';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]       = "str notNull length|7 seekable";
    $props["idnote"]     = "str notNull length|10";
    $props["typenote"]   = "enum notNull list|avec_sans|codage|comprend|exclusion|exemple";
    $props["niveau"]     = "num show|0";
    $props["libelle"]    = "str notNull seekable";
    $props["ordre"]      = "num show|0";
    $props["code_exclu"] = "str length|7 seekable";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code ($this->typenote): $this->libelle";
    $this->_shortview = $this->idnote;
  }
  
  function loadRefCode() {
    return $this->_ref_code = CActiviteCsARR::get($this->code);
  }

  function loadRefCodeExclu() {
    return $this->_ref_code_exclu = CActiviteCsARR::get($this->code_exclu);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefCode();
    $this->loadRefCodeExclu();
  }
}

?>
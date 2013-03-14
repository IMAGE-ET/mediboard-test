<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Note concernant une hiérarchie CsARR
 */
class CNoteHierarchieCsARR extends CCsARRObject {
  
  var $hierarchie        = null;
  var $idnote            = null;
  var $typenote          = null;
  var $niveau            = null;
  var $libelle           = null;
  var $ordre             = null;
  var $hierarchie_exclue = null;
  var $code_exclu        = null;
  
  var $_ref_hierarchie        = null;
  var $_ref_hierarchie_exclue = null;
  var $_ref_activite_exclue   = null;
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'note_hierarchie';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["hierarchie"]        = "str notNull length|11 seekable";
    $props["idnote"]            = "str notNull length|10";
    $props["typenote"]          = "enum notNull list|aut_note|avec_sans|codage|compr_tit|def|exclusion|inclus";
    $props["niveau"]            = "num show|0";
    $props["libelle"]           = "text notNull seekable";
    $props["ordre"]             = "num show|0";
    $props["hierarchie_exclue"] = "str length|11 seekable";
    $props["code_exclu"       ] = "str length|7 seekable";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->hierarchie ($this->typenote): $this->libelle";
    $this->_shortview = $this->idnote;
  }
  
  function loadRefHierarchie() {
    return $this->_ref_hierarchie = CHierarchieCsARR::get($this->hierarchie);
  }

  function loadRefHierarchieExlue() {
    return $this->_ref_hierarchie_exclue = CHierarchieCsARR::get($this->code_exclus);
  }
  
  function loadRefCodeExclu() {
    return $this->_ref_code_exclu = CActiviteCsARR::get($this->code_exclus);
  }
  
	function loadView(){
    parent::loadView();
    $this->loadRefHierarchie();
    $this->loadRefHierarchieExlue();
    $this->loadRefCodeExclu();
  }
}

?>
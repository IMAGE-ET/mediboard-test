<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Note concernant une activite CsARR
 */
class CNoteActiviteCsARR extends CCsARRObject {

  public $code;
  public $idnote;
  public $typenote;
  public $niveau;
  public $libelle;
  public $ordre;
  public $code_exclu;

  public $_ref_code;
  public $_ref_code_exclu;

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
    $props["libelle"]    = "text notNull seekable";
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

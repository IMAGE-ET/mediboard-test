<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Bilan d'entrée SSR
 */
class CBilanSSR extends CMbObject {
  var $_activites = array("kine", "ergo", "psy", "ortho", "diet", "social", "apa");

  // DB Table key
  var $bilan_id = null;
  
  // References
  var $sejour_id = null;

  // DB Fields
  var $kine   = null;
  var $ergo   = null;
  var $psy    = null;
  var $ortho  = null;
  var $diet   = null;
  var $social = null;
  var $apa    = null;
  
  var $entree = null;
  var $sortie = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'bilan_ssr';
    $spec->key   = 'bilan_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"] = "ref notNull class|CSejour";

    $specs["kine"]   = "str autocomplete";
    $specs["ergo"]   = "str autocomplete";
    $specs["psy"]    = "str autocomplete";
    $specs["ortho"]  = "str autocomplete";
    $specs["diet"]   = "str autocomplete";
    $specs["social"] = "str autocomplete";
    $specs["apa"]    = "str autocomplete";

    $specs["entree"] = "text helped";
    $specs["sortie"] = "text helped";
    return $specs;
  }
}

?>
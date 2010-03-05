<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CRPUPassage extends CMbObject {
  // DB Table key
  var $rpu_passage_id      = null;
  
  // DB Fields
  var $rpu_id              = null;
  var $extract_passages_id = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'rpu_passage';
    $spec->key   = 'rpu_passage_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["rpu_id"]              = "ref notNull class|CRPU";
    $specs["extract_passages_id"] = "ref notNull class|CExtractPassages";

    return $specs;
  }  
}
?>
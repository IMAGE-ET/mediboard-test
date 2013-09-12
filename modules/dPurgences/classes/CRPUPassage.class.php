<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CRPUPassage
 */
class CRPUPassage extends CMbObject {
  // DB Table key
  public $rpu_passage_id;
  
  // DB Fields
  public $rpu_id;
  public $extract_passages_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'rpu_passage';
    $spec->key   = 'rpu_passage_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["rpu_id"]              = "ref notNull class|CRPU";
    $specs["extract_passages_id"] = "ref notNull class|CExtractPassages";
    return $specs;
  }  
}

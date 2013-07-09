<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Almost empty class for permissions issues
 */
class CIntervHorsPlage extends CMbObject {
  // DB Table key
  public $interv_hors_plage_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'interv_hors_plages';
    $spec->key   = 'interv_hors_plage_id';
    return $spec;
  }
}

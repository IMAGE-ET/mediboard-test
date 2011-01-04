<?php /* $Id: typeanesth.class.php 9834 2010-08-17 20:50:07Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Almost empty class for permissions issues
 */
class CIntervHorsPlage extends CMbObject {
  // DB Table key
  var $interv_hors_plage_id = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'interv_hors_plages';
    $spec->key   = 'interv_hors_plage_id';
    return $spec;
  }
}
?>
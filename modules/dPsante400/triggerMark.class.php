<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CTriggerMark extends CMbObject {
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'trigger_mark';
    $spec->key   = 'mark_id';
    $spec->loggable = true;
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["markid400"]  = "str notNull maxLength|10";
    $specs["type"]       = "str maxLength|80";
    $specs["last_update"]  = "dateTime notNull";
    return $specs;
  }
}
?>
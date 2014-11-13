<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// right on reservation
CCanDo::checkRead();

// right on dPplanningOp
$pl_op = CModule::getActive("dPplanningOp");
if ($pl_op->canDo()->edit) {
  CAppUI::requireModuleFile("dPplanningOp", "vw_edit_sejour");
}
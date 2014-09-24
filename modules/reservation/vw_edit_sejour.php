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

$module = new CModule();
$module->loadByName("dPplanningOp");
$module->canDo()->needsRead();

CAppUI::requireModuleFile("dPplanningOp", "vw_edit_sejour");
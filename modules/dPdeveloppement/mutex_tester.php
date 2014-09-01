<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$actions = array(
  "stall",
  "die",
  "run",
  "dummy",
);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("actions", $actions);

$smarty->display("mutex_tester.tpl");

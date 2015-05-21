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

// Déverrouiller la session pour rendre possible les requêtes concurrentes.
CSessionHandler::writeClose();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("module", "system");
$smarty->assign("action", "about");
$smarty->display("benchmark.tpl");
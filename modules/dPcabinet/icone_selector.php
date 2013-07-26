<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

// Chargement de la liste des icones presents dans le fichier
$icones = CAppUI::readFiles("modules/dPcabinet/images/categories", ".png");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("icones", $icones);
$smarty->display("icone_selector.tpl");

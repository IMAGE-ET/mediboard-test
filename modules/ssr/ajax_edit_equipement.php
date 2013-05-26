<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Plateau du contexte
$plateau = new CPlateauTechnique;
$plateau->load(CValue::get("plateau_id"));
$plateau->loadRefsEquipements(false);

// Equipement à editer
$equipement = new CEquipement;
$equipement->load(CValue::get("equipement_id"));
$equipement->plateau_id = $plateau->_id;
$equipement->loadRefsNotes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("equipement", $equipement);
$smarty->assign("plateau", $plateau);

$smarty->display("inc_edit_equipement.tpl");

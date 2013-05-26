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

$plateau = new CPlateauTechnique;
$plateau->group_id = CGroups::loadCurrent()->_id;

// Plateaux disponibles
$plateaux = $plateau->loadMatchingList();
foreach ($plateaux as $_plateau) {
  $_plateau->countBackRefs("techniciens");
  $_plateau->countBackRefs("equipements");
}

// Plateau sélectionné
$plateau->load(CValue::getOrSession("plateau_id"));
$plateau->loadRefsNotes();
$plateau->loadRefsEquipements(false);

$date = CMbDT::date();
foreach ($plateau->loadRefsTechniciens(false) as $_technicien) {
  $_technicien->countSejoursDate($date);
}

// Equipement
$equipement = new CEquipement;
$equipement->plateau_id = $plateau->_id;

// Technicien
$technicien = new CTechnicien;
$technicien->plateau_id = $plateau->_id;

// Kinés
$user = new CMediusers;
$kines = $user->loadKines();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("equipement", $equipement);
$smarty->assign("technicien", $technicien);
$smarty->assign("kines", $kines);
$smarty->assign("plateau", $plateau);
$smarty->assign("plateaux", $plateaux);
$smarty->display("vw_idx_plateau.tpl");

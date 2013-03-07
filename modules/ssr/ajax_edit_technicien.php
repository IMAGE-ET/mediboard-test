<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Plateau du contexte
$plateau = new CPlateauTechnique;
$plateau->load(CValue::get("plateau_id"));

// Détails des techniciens
$date = CMbDT::date();
foreach ($plateau->loadRefsTechniciens(false) as $_technicien) {
	$_technicien->countSejoursDate($date);
};

// Technicien à editer
$technicien = new CTechnicien;
$technicien->load(CValue::get("technicien_id"));
$technicien->plateau_id = $plateau->_id;
$technicien->loadRefsNotes();
$technicien->loadRefPlateau(); 
$technicien->loadRefKine(); 
$technicien->countSejoursDate($date);

// Alter egos pour les transferts de séjours
$where["kine_id"] = "= '$technicien->kine_id'";
$alteregos = $technicien->loadList($where);
unset($alteregos[$technicien->_id]);
foreach($alteregos as $_alterego) {
  $_alterego->loadRefPlateau(); 
  $_alterego->loadRefKine(); 
}

// Kinés
$user = new CMediusers;
$kines = $user->loadKines();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("technicien", $technicien);
$smarty->assign("alteregos", $alteregos);
$smarty->assign("plateau", $plateau);
$smarty->assign("kines", $kines);

$smarty->display("inc_edit_technicien.tpl");


?>
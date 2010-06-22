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
$plateau->loadRefsTechniciens();

// Equipement à editer
$technicien = new CTechnicien;
$technicien->load(CValue::get("technicien_id"));
$technicien->plateau_id = $plateau->_id;

// Kinés
$user = new CMediusers;
$kines = $user->loadKines();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("technicien", $technicien);
$smarty->assign("plateau", $plateau);
$smarty->assign("kines", $kines);

$smarty->display("inc_edit_technicien.tpl");


?>
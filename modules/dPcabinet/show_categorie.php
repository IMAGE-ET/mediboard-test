<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);
$categorie = $consult->loadRefCategorie();


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("categorie", $categorie);
$smarty->display("inc_icone_categorie_consult.tpl");

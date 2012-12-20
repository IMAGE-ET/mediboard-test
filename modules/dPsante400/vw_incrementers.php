<?php /* $Id: view_identifiants.php 12379 2011-06-08 10:13:32Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 12379 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$incrementer_id = CValue::getOrSession("incrementer_id");

// Liste des incrémenteurs
$incrementer = new CIncrementer();
$incrementers = $incrementer->loadMatchingList();
foreach ($incrementers as $_incrementer) {
}

// Récupération due l'incrementeur à ajouter/editer 
$incrementer = new CIncrementer;
$incrementer->load($incrementer_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("incrementers", $incrementers);
$smarty->assign("incrementer" , $incrementer);
$smarty->display("vw_incrementers.tpl");

?>
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

// Récupération due l'incrementeur à ajouter/editer 
$incrementer = new CIncrementer;
$incrementer->load($incrementer_id);
$incrementer->loadMasterDomain();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("incrementer" , $incrementer);
$smarty->display("inc_edit_incrementer.tpl");

?>
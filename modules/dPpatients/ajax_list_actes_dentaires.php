<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$devenir_dentaire_id = CValue::get("devenir_dentaire_id");

$devenir_dentaire = new CDevenirDentaire();
$devenir_dentaire->load($devenir_dentaire_id);

$actes_dentaires = $devenir_dentaire->loadRefsActesDentaires();

$smarty = new CSmartyDP;

$smarty->assign("actes_dentaires", $actes_dentaires);

$smarty->display("inc_list_actes_dentaires.tpl");

?>
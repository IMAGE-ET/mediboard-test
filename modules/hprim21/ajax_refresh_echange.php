<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 9914 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echg_hprim21_id = CValue::get("echange_hprim21_id");

// Chargement de l'objet
$echg_hprim21 = new CEchangeHprim21();
$echg_hprim21->load($echg_hprim21_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $echg_hprim21);
$smarty->display("inc_echange_hprim21.tpl");


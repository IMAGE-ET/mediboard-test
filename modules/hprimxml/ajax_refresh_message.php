<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echange_hprim_id         = CValue::get("echange_hprim_id");
$echange_hprim_classname  = CValue::get("echange_hprim_classname");

// Chargement de l'objet
$echange_hprim = new $echange_hprim_classname;
$echange_hprim->load($echange_hprim_id);
$echange_hprim->getObservations();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $echange_hprim);

$smarty->display("inc_echange_hprim.tpl");

?>
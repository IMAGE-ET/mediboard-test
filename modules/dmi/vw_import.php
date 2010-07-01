<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dmi
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$object_class = CValue::get("object_class", "CSociete");

$dosql = ($object_class == "CSociete") ? "do_suppliers_import" : "do_dmi_import";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_class", $object_class);
$smarty->assign("dosql", $dosql);

$smarty->display("vw_import.tpl");

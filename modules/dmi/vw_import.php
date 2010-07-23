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

switch($object_class) {
  case "CSociete":   $dosql = "do_suppliers_import";  break;
  case "CProductReference": $dosql = "do_references_import"; break;
  default:           $dosql = "do_dmi_import";        break;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object_class", $object_class);
$smarty->assign("dosql", $dosql);

$smarty->display("vw_import.tpl");

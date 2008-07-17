<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */
$type_ei_id = mbGetValueFromGetOrSession("type_ei_id");

$type_ei = new CTypeEi();
$type_ei_list = $type_ei->loadlist();

if($type_ei_id) {
  $type_ei = new CTypeEi();
  $type_ei->load($type_ei_id);
}

$smarty = new CSmartyDP();

$smarty->assign("type_ei",$type_ei);
$smarty->assign("type_ei_list",$type_ei_list);
$smarty->display("vw_typeEi_manager.tpl");
?>
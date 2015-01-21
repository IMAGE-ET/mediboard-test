<?php
/**
 * $Id: ajax_edit_libelle.php 4924 2013-12-09 10:47:53Z flavien $
 *
 * @package    Mediboard
 * @subpackage mvsante
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision: 4924 $
 */
CCanDo::checkEdit();
$libelle_id = CValue::get("libelle_id");

$libelle = new CLibelleOp();
$libelle->load($libelle_id);

if (!$libelle->_id) {
  $libelle->group_id = CGroups::loadCurrent()->_id;
}

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("libelle",  $libelle);

$smarty->display("vw_edit_libelle.tpl");

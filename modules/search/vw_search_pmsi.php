<?php 

/**
 * $Id$
 *
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org */

CCanDo::checkRead();
$sejour_id = CValue::get("sejour_id");
$sejour = new CSejour();
$sejour->load($sejour_id);

$types = array();
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("sejour", $sejour);
$smarty->assign("types", $types);
$smarty->display("vw_search_pmsi.tpl");
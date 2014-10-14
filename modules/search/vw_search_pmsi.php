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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("sejour", $sejour);
$smarty->display("vw_search_pmsi.tpl");
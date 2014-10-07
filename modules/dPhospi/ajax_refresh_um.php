<?php 

/**
 * $Id$
 *  
 * @category DPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$um_id = CValue::get("um_id");
$uf_id = CValue::get("uf_id");
$uf = new CUniteFonctionnelle();

if ($uf_id) {
  $uf->load($uf_id);
}

$um = new CUniteMedicale();
$um->load($um_id);

$smarty = new CSmartyDP();
$smarty->assign("um", $um);
$smarty->assign("uf", $uf);
$smarty->display("inc_vw_um_mode_hospit.tpl");
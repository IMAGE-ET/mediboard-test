<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$prestation_id = CValue::getOrSession("prestation_id");
$object_class  = CValue::getOrSession("object_class", "CPrestationPonctuelle");

$smarty = new CSmartyDP;

$smarty->assign("prestation_id", $prestation_id);
$smarty->assign("object_class", $object_class);

$smarty->display("vw_prestations.tpl");


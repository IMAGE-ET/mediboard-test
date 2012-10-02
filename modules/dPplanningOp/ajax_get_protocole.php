<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$protocole_id = CValue::get("protocole_id");
$chir_id      = CValue::get("chir_id");

$protocole = new CProtocole();
$protocole->load($protocole_id);
$protocole->loadRefsFwd();
$protocole->_types_ressources_ids = implode(",", CMbArray::pluck($protocole->loadRefsBesoins(), "type_ressource_id"));

$smarty = new CSmartyDP();

$smarty->assign("chir_id"  , $chir_id);
$smarty->assign("protocole", $protocole);

$smarty->display("inc_get_protocole.tpl");

<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$indispo_ressource_id = CValue::get("indispo_ressource_id");

$indispo = new CIndispoRessource;
$indispo->load($indispo_ressource_id);
$indispo->loadRefRessource();

$smarty = new CSmartyDP;

$smarty->assign("indispo", $indispo);

$smarty->display("inc_edit_indispo.tpl");

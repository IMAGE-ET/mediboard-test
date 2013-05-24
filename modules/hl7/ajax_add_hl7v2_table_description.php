<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$table_description = new CHL7v2TableDescription();
$table_description->user = 1;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("table_description", $table_description);
$smarty->display("inc_add_hl7v2_table_description.tpl");


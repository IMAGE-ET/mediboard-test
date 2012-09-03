<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$fields_email = array(
  "URL",
  "PRATICIEN - NOM",
  "PRATICIEN - PRENOM",
  "DATE INTERVENTION",
  "HEURE INTERVENTION"
);

$smarty = new CSmartyDP();

$smarty->assign("fields_email", $fields_email);
$smarty->assign("hours", range(0, 23));

$smarty->display("configure.tpl");

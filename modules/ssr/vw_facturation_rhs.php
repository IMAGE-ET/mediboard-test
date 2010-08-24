<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CCanDo::checkRead();

// Liste des RHSs
$where = array();
$where['facture'] = " = '0'";

$rhs = new CRHS();
$rhss = $rhs->loadList($where);

$rhss_no_charge = array();
foreach ($rhss as $_rhs) {
  $rhss_no_charge[$_rhs->date_monday][] = $_rhs;
}

ksort($rhss_no_charge);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rhss_no_charge", $rhss_no_charge);
$smarty->display("vw_facturation_rhs.tpl");

?>
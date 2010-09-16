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
$req = new CRequest;
$req->addTable("rhs");
$req->addColumn("date_monday", "mondate");
$req->addColumn("COUNT(*)", "count");
$req->addWhereClause("facture", " = '0'");
$req->addGroup("date_monday");
$ds = $rhs->_spec->ds;
$rhs_counts = $ds->loadList($req->getRequest());
foreach($rhs_counts as &$_rhs_count) {
	$_rhs_count["sundate"] = mbDate("+6 DAYS", $_rhs_count["mondate"]);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rhs_counts", $rhs_counts);
$smarty->display("vw_facturation_rhs.tpl");

?>
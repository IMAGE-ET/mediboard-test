<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CCanDo::checkEdit();

// Liste des RHSs
$rhs = new CRHS();
$req = new CRequest;
$req->addTable("rhs");
$req->addLJoinClause("sejour", "sejour.sejour_id = rhs.sejour_id");
$req->addColumn("date_monday", "mondate");
$req->addColumn("COUNT(*)", "count");
$req->addWhereClause("rhs.facture", " = '0'");
$req->addWhereClause("sejour.annule", " = '0'");
$req->addGroup("date_monday");
$ds = $rhs->_spec->ds;
$rhs_counts = $ds->loadList($req->getRequest());
foreach($rhs_counts as &$_rhs_count) {
	$_rhs_count["sundate"] = CMbDT::date("+6 DAYS", $_rhs_count["mondate"]);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rhs_counts", $rhs_counts);
$smarty->display("vw_facturation_rhs.tpl");

?>
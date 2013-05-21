<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Needle
$keyword = reset($_POST);
$needle = "%$keyword%";

// Query
$select = "SELECT CODE, LIBELLE FROM categorie_socioprofessionnelle";
$where  = "WHERE LIBELLE LIKE '$needle'";
$order  = "ORDER BY CODE";
$query  = "$select $where $order";

$ds = CSQLDataSource::get("INSEE");
$matches = $ds->loadList($query);

// Template
$smarty = new CSmartyDP();

$smarty->assign("keyword", $keyword);
$smarty->assign("matches", $matches);
$smarty->assign("nodebug", true);
  
$smarty->display("inc_csp_autocomplete.tpl");
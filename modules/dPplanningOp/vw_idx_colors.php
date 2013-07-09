<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

// Tous les libelllés
$query = new CRequest;
$query->addTable("sejour");
$query->addColumn("COUNT(libelle)", "libelle_count");
$query->addColumn("libelle");
$query->addWhereClause("type", "= 'ssr'");
$query->addOrder("libelle_count DESC");
$query->addGroup("libelle");

$sejour = new CSejour;
$ds = $sejour->_spec->ds;
$libelle_counts = array();
foreach ($ds->loadList($query->getRequest()) as $row) {
  $libelle_counts[$row["libelle"]] = $row["libelle_count"];
}
unset($libelle_counts[""]);

// Libellés disponibles
$colors = CColorLibelleSejour::loadAllFor(array_keys($libelle_counts));

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("libelle_counts", $libelle_counts);
$smarty->assign("colors", $colors);

$smarty->display("vw_idx_colors.tpl");

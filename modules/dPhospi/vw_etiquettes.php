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

CCanDo::checkEdit();

$modele_etiquette_id = CValue::getOrSession("modele_etiquette_id");
$filter_class        = CValue::getOrSession("filter_class", "all");

$classes = CCompteRendu::getTemplatedClasses();
$classes["CRPU"] = CAppUI::tr("CRPU");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("modele_etiquette_id"   , $modele_etiquette_id);
$smarty->assign("classes"               , $classes);
$smarty->assign("filter_class"          , $filter_class);
$smarty->display("vw_etiquettes.tpl");

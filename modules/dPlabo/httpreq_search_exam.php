<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$listExams = array();

// Récuperation du mot recherché
$recherche = CValue::get("recherche");

// Chargements des analyses correspondantes
$exam = new CExamenLabo();
$limit = "30";
$where["libelle"] = "LIKE '%$recherche%' ";
$where["obsolete"] = " = '0'";
$listExams = $exam->loadList($where, null, $limit);
$countExams = $exam->countList($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("search"    , 1           );
$smarty->assign("recherche" , $recherche  );
$smarty->assign("listExams" , $listExams  );
$smarty->assign("countExams", $countExams );
$smarty->assign("catalogue" , new CCatalogueLabo());

$smarty->display("inc_vw_examens_catalogues.tpl");

<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$nda = CValue::get("nda");

$idex = new CIdSante400();
$where["object_class"] = "= 'CSejour'";
$where["id400"] = "LIKE '$nda%'";
/** @var CIdSante400[] $ideces */
$ideces = $idex->loadList($where, null, "100");
$sejours = array();
foreach ($ideces as $_idex) {
  /** @var CSejour $sejour */
  $sejour = $_idex->loadTargetObject();
  $sejour->loadRefPatient()->loadIPP();
  /** @var CRHS $_rhs */
  foreach ($sejour->loadBackRefs("rhss", "date_monday") as $_rhs) {
    $_rhs->loadRefsNotes();
  }
  $sejours[$sejour->_id] = $sejour; 
} 

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->display("inc_vw_rhs_sejour_search.tpl");

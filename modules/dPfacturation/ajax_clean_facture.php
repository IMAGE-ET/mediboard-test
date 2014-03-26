<?php
/**
 * $Id: ajax_clean_facture.php 19223 2013-05-21 14:44:04Z aurelie17 $
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19223 $
 */

CCanDo::checkAdmin();
$ljoin = array();
$ljoin["facture_cabinet"] = "facture_cabinet.facture_id = facture_liaison.facture_id";
$where = array();
$where["object_class"]  = " = 'CConsultation'";
$where["facture_class"] = " = 'CFactureCabinet'";
$where["facture_cabinet.annule"] = " = '0'";

$group = "object_id HAVING COUNT(object_id) >= 2";

$liaison = new CFactureLiaison();
$liaisons = $liaison->loadList($where, null, null, $group, $ljoin);
foreach ($liaisons as $lien) {
  $fact = $lien->loadRefFacture();
  $fact->loadRefPatient();
  $fact->loadRefPraticien();
  $fact->loadRefsObjects();
  $fact->loadRefsReglements();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("liaisons"  , $liaisons);

$smarty->display("inc_configure_resolutions.tpl");

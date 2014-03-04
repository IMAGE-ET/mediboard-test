<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

$ds = CSQLDataSource::get("ccamV2");

$object_id = CValue::get("object_id");
$object_class = CValue::get("object_class");
$code = CValue::post("code");

// Chargement de l'object
/** @var CCodable $object */
$object = new $object_class;
$object->load($object_id);

// Chargement de ses actes NGAP
$object->countActes();

$user = CMediusers::get();
if ($user->isMedical()) {
  $praticien = $user;
}
else {
  $praticien = $object->loadRefPraticien();
}
$praticien->loadRefFunction();

$praticien->spec_cpam_id ? $spe_undefined = false : $spe_undefined = true;
$praticien->spec_cpam_id ? $spe = $praticien->spec_cpam_id : $spe = 1;

// Creation de la requete permettant de retourner tous les codes correspondants
if ($code) {
  $sql = "SELECT c.`code`, c.`libelle`, c.`lettre_cle`, t.`tarif`
  FROM `codes_ngap` as c, `tarif_ngap` as t, `specialite_to_tarif_ngap` as s
  WHERE c.`code` LIKE ?1 AND t.`code` = c.`code` AND t.`zone` = ?2 AND s.`specialite` = ?3 AND t.`tarif_ngap_id` = s.`tarif_id`";

  if (!$object->_count_actes) {
    $sql .= " AND c.`lettre_cle` = '1' ";
  }
}
$sql = $ds->prepare($sql, addslashes($code) . '%', CActeNGAP::getZone($praticien->_ref_function), $spe);

$result = $ds->loadList($sql, null);

// Création du template
$smarty = new CSmartyDP();
$smarty->debugging = false;

$smarty->assign("code"      , $code);
$smarty->assign("result"    , $result);
$smarty->assign('spe_undefined', $spe_undefined);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_ngap_autocomplete.tpl");

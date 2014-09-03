<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$object_guid = CValue::getOrSession("object_guid");

/** @var CCodable $objet */
$objet = CMbObject::loadFromGuid($object_guid);
$objet->loadRefsActes();
foreach ($objet->_ref_actes_ccam as &$acte) {
  $acte->loadRefsFwd();
}
if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
  $objet->guessActesAssociation();
}
else {
  foreach ($objet->_ref_actes_ccam as &$acte) {
    $acte->guessAssociation();
  }
}

$sejour = new CSejour();
if ($objet->_class == "CSejour") {
  $sejour = $objet;
}
else {
  $sejour->_id = $objet->sejour_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objet" , $objet);
$smarty->assign("sejour", $sejour);

$smarty->display("inc_confirm_actes_ccam.tpl");
